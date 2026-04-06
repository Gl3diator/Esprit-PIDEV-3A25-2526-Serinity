<?php

namespace App\Controller;

use App\Entity\Sommeil;
use App\Form\SommeilType;
use App\Repository\SommeilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sommeil')]
final class SommeilController extends AbstractController
{
    #[Route('', name: 'app_sommeil_index', methods: ['GET'])]
    public function index(Request $request, SommeilRepository $sommeilRepository): Response
    {
        $filters = [
            'q' => $request->query->get('q'),
            'qualite' => $request->query->get('qualite'),
            'humeur' => $request->query->get('humeur'),
            'insuffisant' => $request->query->get('insuffisant'),
            'sort' => $request->query->get('sort', 'date_nuit'),
            'direction' => $request->query->get('direction', 'DESC'),
        ];

        $sommeils = $sommeilRepository->findFrontFiltered($filters);
        $stats = $sommeilRepository->getFrontStats();

        return $this->render('sommeil/list.html.twig', [
            'sommeils' => $sommeils,
            'filters' => $filters,
            'stats' => $stats,
        ]);
    }

    #[Route('/list', name: 'app_sommeil_list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): Response
    {
        $sommeils = $em->getRepository(Sommeil::class)->findAll();

        return $this->render('sommeil/list.html.twig', [
            'sommeils' => $sommeils,
            'filters' => [],
            'stats' => null,
        ]);
    }

    #[Route('/new', name: 'app_sommeil_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $sommeil = new Sommeil();
        $form = $this->createForm(SommeilType::class, $sommeil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sommeil->setCreatedAt(new \DateTime());
            $sommeil->setUpdatedAt(new \DateTime());
            $sommeil->setUserId(1);

            $em->persist($sommeil);
            $em->flush();

            $this->addFlash('success', 'Nuit de sommeil ajoutée avec succès !');

            return $this->redirectToRoute('app_sommeil_index');
        }

        return $this->render('sommeil/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/show/{id<\d+>}', name: 'app_sommeil_show', methods: ['GET'])]
    public function show(Sommeil $sommeil): Response
    {
        return $this->render('sommeil/show.html.twig', [
            'sommeil' => $sommeil,
        ]);
    }

    #[Route('/edit/{id<\d+>}', name: 'app_sommeil_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sommeil $sommeil, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SommeilType::class, $sommeil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sommeil->setUpdatedAt(new \DateTime());
            $em->flush();

            $this->addFlash('success', 'Nuit de sommeil modifiée avec succès !');

            return $this->redirectToRoute('app_sommeil_index');
        }

        return $this->render('sommeil/edit.html.twig', [
            'sommeil' => $sommeil,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id<\d+>}', name: 'app_sommeil_delete', methods: ['POST'])]
    public function delete(Request $request, Sommeil $sommeil, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $sommeil->getId(), $request->request->get('_token'))) {
            $em->remove($sommeil);
            $em->flush();

            $this->addFlash('success', 'Nuit de sommeil supprimée.');
        }

        return $this->redirectToRoute('app_sommeil_index');
    }

    #[Route('/export/csv', name: 'app_sommeil_export_csv', methods: ['GET'])]
    public function exportCsv(Request $request, SommeilRepository $sommeilRepository): Response
    {
        $filters = [
            'q' => $request->query->get('q'),
            'qualite' => $request->query->get('qualite'),
            'humeur' => $request->query->get('humeur'),
            'insuffisant' => $request->query->get('insuffisant'),
            'sort' => $request->query->get('sort', 'date_nuit'),
            'direction' => $request->query->get('direction', 'DESC'),
        ];

        $rows = $sommeilRepository->findFrontFiltered($filters);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="sommeils.csv"');

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['Date', 'Heure coucher', 'Heure réveil', 'Durée', 'Qualité', 'Humeur', 'Statut']);

        foreach ($rows as $s) {
            fputcsv($handle, [
                $s->getDateNuit()?->format('Y-m-d'),
                $s->getHeureCoucher(),
                $s->getHeureReveil(),
                $s->getDureeSommeil(),
                $s->getQualite(),
                $s->getHumeurReveil(),
                method_exists($s, 'isSommeilInsuffisant') && $s->isSommeilInsuffisant()
                    ? 'Sommeil insuffisant'
                    : 'Normal',
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        $response->setContent($content);

        return $response;
    }

    #[Route('/export/pdf', name: 'app_sommeil_export_pdf', methods: ['GET'])]
    public function exportPdf(Request $request, SommeilRepository $sommeilRepository): Response
    {
        $filters = [
            'q' => $request->query->get('q'),
            'qualite' => $request->query->get('qualite'),
            'humeur' => $request->query->get('humeur'),
            'insuffisant' => $request->query->get('insuffisant'),
            'sort' => $request->query->get('sort', 'date_nuit'),
            'direction' => $request->query->get('direction', 'DESC'),
        ];

        return $this->render('sommeil/export_pdf.html.twig', [
            'sommeils' => $sommeilRepository->findFrontFiltered($filters),
        ]);
    }
}