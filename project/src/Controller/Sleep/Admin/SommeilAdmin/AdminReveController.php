<?php

namespace App\Controller\Sleep\Admin\SommeilAdmin; // ✅ namespace corrigé

use App\Entity\Sleep\Reves;                         // ✅ import corrigé
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/reve', name: 'app_admin_reve_')]
final class AdminReveController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $reveEntities = $entityManager->getRepository(Reves::class)->findBy([], ['id' => 'DESC']);

        $reves = [];

        foreach ($reveEntities as $reve) {
            $sommeil = $reve->getSommeil(); // ✅ getSommeil() (plus getSommeilId())

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
                'humeur_class' => $this->mapEmotionClass($humeur),
            ];
        }

        return $this->render('sleep/admin/sommeil.html.twig', [
            'reves' => $reves,
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
