<?php

namespace App\Controller\admin;

use App\Entity\Sommeil;
use App\Entity\Reves;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/sommeil', name: 'app_admin_sommeil_')]
final class AdminSommeilController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $sommeilEntities = $entityManager->getRepository(Sommeil::class)->findBy([], ['id' => 'DESC']);
        $reveEntities = $entityManager->getRepository(Reves::class)->findBy([], ['id' => 'DESC']);

        $qualityMap = [
            'Excellente' => 100,
            'Bonne' => 75,
            'Moyenne' => 50,
            'Mauvaise' => 25,
        ];

        $sommeils = [];
        $reves = [];

        $totalDuree = 0;
        $totalQualite = 0;

        foreach ($sommeilEntities as $sommeil) {
            $qualiteLabel = $sommeil->getQualite();
            $qualiteScore = $qualityMap[$qualiteLabel] ?? 0;

            $statutClass = 'status-bad';
            $statutLabel = 'Mauvais';

            if ($qualiteScore >= 70) {
                $statutClass = 'status-good';
                $statutLabel = 'Bon';
            } elseif ($qualiteScore >= 40) {
                $statutClass = 'status-warn';
                $statutLabel = 'Moyen';
            }

            $duree = $sommeil->getDureeSommeil() ?? 0;
            $totalDuree += $duree;
            $totalQualite += $qualiteScore;

            $userId = $sommeil->getUserId();

            $sommeils[] = [
                'id' => $sommeil->getId(),
                'user_id' => $userId,
                'user_label' => $userId ? 'Utilisateur #' . $userId : 'Utilisateur inconnu',
                'user_avatar' => 'U',
                'date_nuit' => $sommeil->getDateNuit(),
                'duree' => $duree,
                'qualite_label' => $qualiteLabel ?? '-',
                'qualite_score' => $qualiteScore,
                'heure_coucher' => $sommeil->getHeureCoucher() ?? '-',
                'heure_reveil' => $sommeil->getHeureReveil() ?? '-',
                'statut_label' => $statutLabel,
                'statut_class' => $statutClass,
            ];
        }

        foreach ($reveEntities as $reve) {
            $sommeil = $reve->getSommeilId();

            $userId = null;
            $dateNuit = null;

            if ($sommeil) {
                $userId = $sommeil->getUserId();
                $dateNuit = $sommeil->getDateNuit();
            }

            $humeur = $reve->getHumeur() ?? 'Neutre';

            $reves[] = [
                'id' => $reve->getId(),
                'user_id' => $userId,
                'user_label' => $userId ? 'Utilisateur #' . $userId : 'Utilisateur inconnu',
                'user_avatar' => 'U',
                'date_nuit' => $dateNuit,
                'titre' => $reve->getTitre() ?? 'Sans titre',
                'description' => $reve->getDescription() ?? '',
                'description_courte' => mb_strimwidth($reve->getDescription() ?? '', 0, 60, '…'),
                'type_reve' => $reve->getTypeReve() ?? 'Inconnu',
                'humeur' => $humeur,
                'humeur_class' => 'emotion-' . strtolower(str_replace(' ', '-', $humeur)),
            ];
        }

        $kpis = [
            'total_sommeils' => count($sommeils),
            'total_reves' => count($reves),
            'moyenne_duree' => count($sommeils) > 0 ? round($totalDuree / count($sommeils), 1) : 0,
            'moyenne_qualite' => count($sommeils) > 0 ? round($totalQualite / count($sommeils)) : 0,
        ];

        return $this->render('admin/sommeil.html.twig', [
            'sommeils' => $sommeils,
            'reves' => $reves,
            'kpis' => $kpis,
        ]);
    }
}