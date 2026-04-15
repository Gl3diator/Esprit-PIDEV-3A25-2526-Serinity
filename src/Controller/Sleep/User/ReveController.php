<?php

namespace App\Controller\Sleep\User; // ✅ namespace corrigé

use App\Entity\Sleep\Reves;          // ✅ import corrigé
use App\Form\Sleep\ReveType;         // ✅ import corrigé (à vérifier selon ton arbo)
use App\Repository\Sleep\RevesRepository; // ✅ import corrigé
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
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
            'q'          => $request->query->get('q'),
            'type'       => $request->query->get('type'),
            'recurrent'  => $request->query->get('recurrent', ''),
            'couleur'    => $request->query->get('couleur'),
            'cauchemars' => $request->query->get('cauchemars'),
            'sort'       => $request->query->get('sort', 'date'),
            'direction'  => $request->query->get('direction', 'DESC'),
        ];

        $reves = $revesRepository->findFrontFiltered($filters);
        $stats = $revesRepository->getFrontStats();

        $dreamMoodCounts = [
            '😄 Joyeux'  => 0,
            '😢 Triste'  => 0,
            '😨 Effrayé' => 0,
            '😐 Neutre'  => 0,
        ];

        foreach ($reves as $reve) {
            $humeur = trim((string) ($reve->getHumeur() ?? ''));
            if (array_key_exists($humeur, $dreamMoodCounts)) {
                $dreamMoodCounts[$humeur]++;
            }
        }

        $stats['dream_mood_labels'] = array_keys($dreamMoodCounts);
        $stats['dream_mood_data']   = array_values($dreamMoodCounts);

        return $this->render('sleep/reve/index.html.twig', [
            'reves'   => $reves,
            'filters' => $filters,
            'stats'   => $stats,
        ]);
    }

    #[Route('/new', name: 'app_reve_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $reve = new Reves();
        $form = $this->createForm(ReveType::class, $reve);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $reve->setCreatedAt(new \DateTime());
                $reve->setUpdatedAt(new \DateTime());

                $em->persist($reve);
                $em->flush();

                $this->addFlash('success', 'Rêve ajouté avec succès !');

                return $this->redirectToRoute('app_reve_index');
            }

            foreach ($this->getFormErrors($form) as $error) {
                $this->addFlash('error', $error);
            }
        }

        return $this->render('sleep/reve/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/show/{id<\d+>}', name: 'app_reve_show', methods: ['GET'])]
    public function show(Reves $reve): Response
    {
        return $this->render('sleep/reve/show.html.twig', [
            'reve' => $reve,
        ]);
    }

    #[Route('/edit/{id<\d+>}', name: 'app_reve_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reves $reve, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ReveType::class, $reve);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $reve->setUpdatedAt(new \DateTime());
                $em->flush();

                $this->addFlash('success', 'Rêve modifié avec succès !');

                return $this->redirectToRoute('app_reve_index');
            }

            foreach ($this->getFormErrors($form) as $error) {
                $this->addFlash('error', $error);
            }
        }

        return $this->render('sleep/reve/edit.html.twig', [
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
            'q'          => $request->query->get('q'),
            'type'       => $request->query->get('type'),
            'recurrent'  => $request->query->get('recurrent', ''),
            'couleur'    => $request->query->get('couleur'),
            'cauchemars' => $request->query->get('cauchemars'),
            'sort'       => $request->query->get('sort', 'date'),
            'direction'  => $request->query->get('direction', 'DESC'),
        ];

        $rows = $revesRepository->findFrontFiltered($filters);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="reves.csv"');

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['Date', 'Titre', 'Type', 'Intensité', 'Récurrent', 'Couleur']);

        foreach ($rows as $r) {
            fputcsv($handle, [
                $r->getCreatedAt()?->format('Y-m-d') ?? '',
                $r->getTitre(),
                $r->getTypeReve(),
                $r->getIntensite(),
                $r->isRecurrent() ? 'Oui' : 'Non', // ✅ isRecurrent()
                $r->isCouleur() ? 'Oui' : 'Non',   // ✅ isCouleur()
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
            'q'          => $request->query->get('q'),
            'type'       => $request->query->get('type'),
            'recurrent'  => $request->query->get('recurrent', ''),
            'couleur'    => $request->query->get('couleur'),
            'cauchemars' => $request->query->get('cauchemars'),
            'sort'       => $request->query->get('sort', 'date'),
            'direction'  => $request->query->get('direction', 'DESC'),
        ];

        $reves = $revesRepository->findFrontFiltered($filters);

        $html = $this->renderView('sleep/reve/export_pdf.html.twig', [
            'reves' => $reves,
        ]);

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="reves.pdf"',
            ]
        );
    }

    private function getFormErrors(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors(true) as $error) {
            $message = $error->getMessage();
            if (!in_array($message, $errors, true)) {
                $errors[] = $message;
            }
        }

        return $errors;
    }
}
