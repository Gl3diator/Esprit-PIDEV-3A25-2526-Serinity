<?php

namespace App\Controller\admin;

use App\Repository\RevesRepository;
use App\Repository\SommeilRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminDashboardController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard', methods: ['GET'])]
    public function index(
        SommeilRepository $sommeilRepository,
        RevesRepository $revesRepository,
    ): Response {
        $sommeils = $sommeilRepository->findBy([], ['date_nuit' => 'DESC']);
        $reves = $revesRepository->findBy([], ['created_at' => 'DESC']);

        $totalSommeils = count($sommeils);
        $totalReves = count($reves);

        $totalDuree = 0.0;
        $countDuree = 0;

        $qualityDistribution = [
            'Excellente' => 0,
            'Bonne' => 0,
            'Moyenne' => 0,
            'Mauvaise' => 0,
        ];

        foreach ($sommeils as $sommeil) {
            if ($sommeil->getDureeSommeil() !== null) {
                $totalDuree += (float) $sommeil->getDureeSommeil();
                ++$countDuree;
            }

            $qualite = $sommeil->getQualite() ?? 'Mauvaise';
            if (!array_key_exists($qualite, $qualityDistribution)) {
                $qualityDistribution[$qualite] = 0;
            }
            ++$qualityDistribution[$qualite];
        }

        $dreamTypeDistribution = [
            'Normal' => 0,
            'Lucide' => 0,
            'Cauchemar' => 0,
        ];

        $recentActivities = [];

        foreach ($sommeils as $sommeil) {
            $recentActivities[] = [
                'type' => 'Sommeil',
                'date_sort' => $sommeil->getDateNuit()?->format('Y-m-d') ?? '',
                'date_label' => $sommeil->getDateNuit()?->format('d/m/Y') ?? '-',
                'title' => 'Nuit du ' . ($sommeil->getDateNuit()?->format('d/m/Y') ?? '-'),
                'metric' => ($sommeil->getDureeSommeil() ?? 0) . ' h',
                'status' => $sommeil->getQualite() ?? '-',
                'user' => $sommeil->getUserId() ? 'Utilisateur #' . $sommeil->getUserId() : 'Utilisateur inconnu',
                'route' => 'app_admin_sommeil_index',
            ];
        }

        foreach ($reves as $reve) {
            $typeReve = $reve->getTypeReve() ?? 'Normal';
            if (!array_key_exists($typeReve, $dreamTypeDistribution)) {
                $dreamTypeDistribution[$typeReve] = 0;
            }
            ++$dreamTypeDistribution[$typeReve];

            $sommeilAssocie = $reve->getSommeilId();

            $recentActivities[] = [
                'type' => 'Rêve',
                'date_sort' => $reve->getCreatedAt()?->format('Y-m-d H:i:s') ?? '',
                'date_label' => $reve->getCreatedAt()?->format('d/m/Y') ?? '-',
                'title' => $reve->getTitre() ?: 'Sans titre',
                'metric' => 'Intensité ' . ($reve->getIntensite() ?? '-'),
                'status' => $typeReve,
                'user' => ($sommeilAssocie && $sommeilAssocie->getUserId())
                    ? 'Utilisateur #' . $sommeilAssocie->getUserId()
                    : 'Utilisateur inconnu',
                'route' => 'app_admin_sommeil_index',
            ];
        }

        usort(
            $recentActivities,
            static fn(array $a, array $b): int => strcmp($b['date_sort'], $a['date_sort'])
        );

        $recentActivities = array_slice($recentActivities, 0, 12);

        $nightmares = $dreamTypeDistribution['Cauchemar'] ?? 0;
        $lucidDreams = $dreamTypeDistribution['Lucide'] ?? 0;
        $normalDreams = $dreamTypeDistribution['Normal'] ?? 0;

        return $this->render('admin/dashboard.html.twig', [
            'total_users' => 0,
            'total_sommeils' => $totalSommeils,
            'total_reves' => $totalReves,
            'total_exercices' => 0,
            'total_humeurs' => 0,
            'total_consultations' => 0,
            'avg_sleep_duration' => $countDuree > 0 ? round($totalDuree / $countDuree, 1) : 0,
            'nightmares_count' => $nightmares,
            'lucid_dreams_count' => $lucidDreams,
            'normal_dreams_count' => $normalDreams,
            'quality_distribution' => $qualityDistribution,
            'dream_type_distribution' => $dreamTypeDistribution,
            'recent_activities' => $recentActivities,
        ]);
    }
}