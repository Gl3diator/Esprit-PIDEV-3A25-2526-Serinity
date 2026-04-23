<?php

namespace App\Controller\Sleep\Admin\SommeilAdmin; // ✅ namespace corrigé

use App\Entity\Sleep\Reves;   // ✅ import corrigé
use App\Entity\Sleep\Sommeil; // ✅ import corrigé
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

        $totalDuree = 0.0;
        $totalQualite = 0;
        $countSommeilsAvecQualite = 0;

        $qualiteExcellente = 0;
        $qualiteBonne = 0;
        $qualiteMoyenne = 0;
        $qualiteMauvaise = 0;

        $sleepDurationLabels = [];
        $sleepDurationData = [];

        $wakeMoodCounts = [
            'Reposé' => 0,
            'Joyeux' => 0,
            'Neutre' => 0,
            'Fatigué' => 0,
            'Énergisé' => 0,
        ];

        foreach ($sommeilEntities as $sommeil) {
            $qualiteLabel = $sommeil->getQualite() ?? 'Mauvaise';
            $qualiteScore = $qualityMap[$qualiteLabel] ?? 0;

            $statutClass = 'status-bad';
            $statutLabel = 'À améliorer';

            if ($qualiteScore >= 70) {
                $statutClass = 'status-good';
                $statutLabel = 'Bon';
            } elseif ($qualiteScore >= 40) {
                $statutClass = 'status-warn';
                $statutLabel = 'Moyen';
            }

            $duree = (float) ($sommeil->getDureeSommeil() ?? 0);
            $totalDuree += $duree;

            if ($qualiteScore > 0) {
                $totalQualite += $qualiteScore;
                $countSommeilsAvecQualite++;
            }

            switch ($qualiteLabel) {
                case 'Excellente': $qualiteExcellente++; break;
                case 'Bonne':      $qualiteBonne++;      break;
                case 'Moyenne':    $qualiteMoyenne++;    break;
                case 'Mauvaise':   $qualiteMauvaise++;   break;
            }

            $dateLabel = $sommeil->getDateNuit()?->format('d/m') ?? ('#' . $sommeil->getId());
            $sleepDurationLabels[] = $dateLabel;
            $sleepDurationData[] = $duree;

            $humeurReveil = trim((string) ($sommeil->getHumeurReveil() ?? ''));
            $humeurReveilClean = str_replace(
                ['😌 ', '😄 ', '😐 ', '😴 ', '⚡ '],
                '',
                $humeurReveil
            );

            if (array_key_exists($humeurReveilClean, $wakeMoodCounts)) {
                $wakeMoodCounts[$humeurReveilClean]++;
            }

            $userId = $sommeil->getUserId();

            $sommeils[] = [
                'id'            => $sommeil->getId(),
                'user_id'       => $userId,
                'user_label'    => 'Utilisateur #' . $userId,
                'user_avatar'   => 'U',
                'date_nuit'     => $sommeil->getDateNuit(),
                'duree'         => $duree,
                'qualite_label' => $qualiteLabel,
                'qualite_score' => $qualiteScore,
                'heure_coucher' => $sommeil->getHeureCoucher() ?? '-',
                'heure_reveil'  => $sommeil->getHeureReveil() ?? '-',
                'statut_label'  => $statutLabel,
                'statut_class'  => $statutClass,
            ];
        }

        $revesNormaux = 0;
        $revesLucides = 0;
        $revesCauchemars = 0;
        $revesPremonitoires = 0;

        $dreamMoodCounts = [
            'Joyeux'  => 0,
            'Triste'  => 0,
            'Effrayé' => 0,
            'Serein'  => 0,
            'Neutre'  => 0,
        ];

        foreach ($reveEntities as $reve) {
            $sommeil = $reve->getSommeil(); // ✅ getSommeil() (plus getSommeilId())

            $userId  = null;
            $dateNuit = null;

            if ($sommeil) {
                $userId   = $sommeil->getUserId();
                $dateNuit = $sommeil->getDateNuit();
            }

            $humeur   = $reve->getHumeur() ?? 'Neutre';
            $typeReve = $reve->getTypeReve() ?? 'Inconnu';

            $typeNormalize = mb_strtolower(trim($typeReve));
            if ($typeNormalize === 'normal') {
                $revesNormaux++;
            } elseif ($typeNormalize === 'lucide') {
                $revesLucides++;
            } elseif ($typeNormalize === 'cauchemar') {
                $revesCauchemars++;
            } elseif (in_array($typeNormalize, ['prémonitoire', 'premonitoire'])) {
                $revesPremonitoires++;
            }

            $humeurClean = str_replace(
                ['😄 ', '😢 ', '😨 ', '😌 ', '😐 '],
                '',
                $humeur
            );

            if (array_key_exists($humeurClean, $dreamMoodCounts)) {
                $dreamMoodCounts[$humeurClean]++;
            }

            $reves[] = [
                'id'               => $reve->getId(),
                'user_id'          => $userId,
                'user_label'       => $userId ? 'Utilisateur #' . $userId : 'Utilisateur inconnu',
                'user_avatar'      => 'U',
                'date_nuit'        => $dateNuit,
                'titre'            => $reve->getTitre() ?? 'Sans titre',
                'description'      => $reve->getDescription() ?? '',
                'description_courte' => mb_strimwidth($reve->getDescription() ?? '', 0, 60, '…'),
                'type_reve'        => $typeReve,
                'humeur'           => $humeur,
                'humeur_class'     => $this->mapEmotionClass($humeur),
            ];
        }

        $kpis = [
            'total_sommeils'       => count($sommeils),
            'total_reves'          => count($reves),
            'moyenne_duree'        => count($sommeils) > 0 ? round($totalDuree / count($sommeils), 1) : 0,
            'moyenne_qualite'      => $countSommeilsAvecQualite > 0 ? round($totalQualite / $countSommeilsAvecQualite) : 0,
            'qualite_excellente'   => $qualiteExcellente,
            'qualite_bonne'        => $qualiteBonne,
            'qualite_moyenne'      => $qualiteMoyenne,
            'qualite_mauvaise'     => $qualiteMauvaise,
            'reves_normaux'        => $revesNormaux,
            'reves_lucides'        => $revesLucides,
            'reves_cauchemars'     => $revesCauchemars,
            'reves_premonitoires'  => $revesPremonitoires,
            'sleep_duration_labels' => $sleepDurationLabels,
            'sleep_duration_data'  => $sleepDurationData,
            'wake_mood_labels'     => array_keys($wakeMoodCounts),
            'wake_mood_data'       => array_values($wakeMoodCounts),
            'dream_mood_labels'    => array_keys($dreamMoodCounts),
            'dream_mood_data'      => array_values($dreamMoodCounts),
        ];

        return $this->render('sleep/admin/sommeil.html.twig', [
            'sommeils' => $sommeils,
            'reves'    => $reves,
            'kpis'     => $kpis,
        ]);
    }

    private function mapEmotionClass(string $humeur): string
    {
        $value = mb_strtolower(trim($humeur));

        if (str_contains($value, 'joy') || str_contains($value, 'heureux') || str_contains($value, 'heureuse')) {
            return 'emotion-joyeux';
        }
        if (str_contains($value, 'peur') || str_contains($value, 'effray') || str_contains($value, 'angoisse') || str_contains($value, 'anx')) {
            return 'emotion-peur';
        }
        if (str_contains($value, 'serein') || str_contains($value, 'calme')) {
            return 'emotion-calme';
        }
        if (str_contains($value, 'triste')) {
            return 'emotion-triste';
        }

        return 'emotion-neutre';
    }
}
