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
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/sommeil')]
final class SommeilController extends AbstractController
{
    #[Route('/', name: 'app_sommeil_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $sommeils = $em->getRepository(Sommeil::class)->findAll();

        return $this->render('sommeil/index.html.twig', [
            'sommeils' => $sommeils,
        ]);
    }

    #[Route('/list', name: 'app_sommeil_list', methods: ['GET'])]
    public function list(Request $request, SommeilRepository $sommeilRepository): Response
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

        $qualityCounts = [
            'Excellente' => 0,
            'Bonne' => 0,
            'Moyenne' => 0,
            'Mauvaise' => 0,
        ];

        $wakeMoodCounts = [
            'Reposé' => 0,
            'Joyeux' => 0,
            'Neutre' => 0,
            'Fatigué' => 0,
            'Énergisé' => 0,
        ];

        $sleepDurationLabels = [];
        $sleepDurationData = [];

        foreach ($sommeils as $sommeil) {
            $qualite = $sommeil->getQualite() ?? '';
            if (array_key_exists($qualite, $qualityCounts)) {
                $qualityCounts[$qualite]++;
            }

            $dateLabel = $sommeil->getDateNuit()?->format('d/m') ?? ('#' . $sommeil->getId());
            $sleepDurationLabels[] = $dateLabel;
            $sleepDurationData[] = (float) ($sommeil->getDureeSommeil() ?? 0);

            $humeur = trim((string) ($sommeil->getHumeurReveil() ?? ''));
            $humeurClean = str_replace(
                ['😌 ', '😄 ', '😐 ', '😴 ', '⚡ '],
                '',
                $humeur
            );

            if (array_key_exists($humeurClean, $wakeMoodCounts)) {
                $wakeMoodCounts[$humeurClean]++;
            }
        }

        $stats['avg_quality'] = (int) round(
            (
                $qualityCounts['Excellente'] * 100 +
                $qualityCounts['Bonne'] * 75 +
                $qualityCounts['Moyenne'] * 50 +
                $qualityCounts['Mauvaise'] * 25
            ) / max(count($sommeils), 1)
        );

        $stats['qualite_excellente'] = $qualityCounts['Excellente'];
        $stats['qualite_bonne'] = $qualityCounts['Bonne'];
        $stats['qualite_moyenne'] = $qualityCounts['Moyenne'];
        $stats['qualite_mauvaise'] = $qualityCounts['Mauvaise'];

        $stats['sleep_duration_labels'] = $sleepDurationLabels;
        $stats['sleep_duration_data'] = $sleepDurationData;

        $stats['wake_mood_labels'] = array_keys($wakeMoodCounts);
        $stats['wake_mood_data'] = array_values($wakeMoodCounts);

        return $this->render('sommeil/list.html.twig', [
            'sommeils' => $sommeils,
            'filters' => $filters,
            'stats' => $stats,
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

            return $this->redirectToRoute('app_sommeil_list');
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

            return $this->redirectToRoute('app_sommeil_list');
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

        return $this->redirectToRoute('app_sommeil_list');
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

        $sommeils = $sommeilRepository->findFrontFiltered($filters);

        $html = $this->renderView('sommeil/export_pdf.html.twig', [
            'sommeils' => $sommeils,
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
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="sommeils.pdf"',
            ]
        );
    }
}