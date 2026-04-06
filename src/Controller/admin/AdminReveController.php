<?php

namespace App\Controller\admin;

use App\Entity\Reves;
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

        return $this->render('admin/reve/index.html.twig', [
            'reves' => $reves,
        ]);
    }
}