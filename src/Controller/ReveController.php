<?php

namespace App\Controller;

use App\Entity\Reves;
use App\Form\ReveType;
use App\Repository\RevesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reve')]
final class ReveController extends AbstractController
{
    #[Route('', name: 'app_reve_index', methods: ['GET'])]
    public function index(Request $request, RevesRepository $revesRepository): Response
    {
        $filters = [
            'q' => $request->query->get('q'),
            'type' => $request->query->get('type'),
            'recurrent' => $request->query->get('recurrent', ''),
            'couleur' => $request->query->get('couleur'),
            'cauchemars' => $request->query->get('cauchemars'),
            'sort' => $request->query->get('sort', 'date'),
            'direction' => $request->query->get('direction', 'DESC'),
        ];

        $reves = $revesRepository->findFrontFiltered($filters);
        $stats = $revesRepository->getFrontStats();

        return $this->render('reve/index.html.twig', [
            'reves' => $reves,
            'filters' => $filters,
            'stats' => $stats,
        ]);
    }

    #[Route('/new', name: 'app_reve_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $reve = new Reves();
        $form = $this->createForm(ReveType::class, $reve);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reve->setCreatedAt(new \DateTime());
            $reve->setUpdatedAt(new \DateTime());

            $em->persist($reve);
            $em->flush();

            $this->addFlash('success', 'Rêve ajouté avec succès !');

            return $this->redirectToRoute('app_reve_index');
        }

        return $this->render('reve/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/show/{id<\d+>}', name: 'app_reve_show', methods: ['GET'])]
    public function show(Reves $reve): Response
    {
        return $this->render('reve/show.html.twig', [
            'reve' => $reve,
        ]);
    }

    #[Route('/edit/{id<\d+>}', name: 'app_reve_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reves $reve, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ReveType::class, $reve);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reve->setUpdatedAt(new \DateTime());
            $em->flush();

            $this->addFlash('success', 'Rêve modifié avec succès !');

            return $this->redirectToRoute('app_reve_index');
        }

        return $this->render('reve/edit.html.twig', [
            'reve' => $reve,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id<\d+>}', name: 'app_reve_delete', methods: ['POST'])]
    public function delete(Request $request, Reves $reve, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reve->getId(), $request->request->get('_token'))) {
            $em->remove($reve);
            $em->flush();

            $this->addFlash('success', 'Rêve supprimé.');
        }

        return $this->redirectToRoute('app_reve_index');
    }

    #[Route('/export/csv', name: 'app_reve_export_csv', methods: ['GET'])]
    public function exportCsv(Request $request, RevesRepository $revesRepository): Response
    {
        $filters = [
            'q' => $request->query->get('q'),
            'type' => $request->query->get('type'),
            'recurrent' => $request->query->get('recurrent', ''),
            'couleur' => $request->query->get('couleur'),
            'cauchemars' => $request->query->get('cauchemars'),
            'sort' => $request->query->get('sort', 'date'),
            'direction' => $request->query->get('direction', 'DESC'),
        ];

        $rows = $revesRepository->findFrontFiltered($filters);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="reves.csv"');

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['Date', 'Titre', 'Type', 'Intensité', 'Anxiété', 'Récurrent', 'Couleur']);

        foreach ($rows as $r) {
            fputcsv($handle, [
                method_exists($r, 'getDateReve') && $r->getDateReve() ? $r->getDateReve()->format('Y-m-d') : '',
                method_exists($r, 'getTitre') ? $r->getTitre() : '',
                method_exists($r, 'getTypeReve') ? $r->getTypeReve() : '',
                method_exists($r, 'getIntensite') ? $r->getIntensite() : '',
                method_exists($r, 'getNiveauAnxiete') ? $r->getNiveauAnxiete() : '',
                method_exists($r, 'isRecurrent') ? ($r->isRecurrent() ? 'Oui' : 'Non') : '',
                method_exists($r, 'getCouleur') ? $r->getCouleur() : '',
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        $response->setContent($content);

        return $response;
    }

    #[Route('/export/pdf', name: 'app_reve_export_pdf', methods: ['GET'])]
    public function exportPdf(Request $request, RevesRepository $revesRepository): Response
    {
        $filters = [
            'q' => $request->query->get('q'),
            'type' => $request->query->get('type'),
            'recurrent' => $request->query->get('recurrent', ''),
            'couleur' => $request->query->get('couleur'),
            'cauchemars' => $request->query->get('cauchemars'),
            'sort' => $request->query->get('sort', 'date'),
            'direction' => $request->query->get('direction', 'DESC'),
        ];

        return $this->render('reve/export_pdf.html.twig', [
            'reves' => $revesRepository->findFrontFiltered($filters),
        ]);
    }
}