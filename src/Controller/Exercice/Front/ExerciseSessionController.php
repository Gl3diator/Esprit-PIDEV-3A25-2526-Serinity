<?php

namespace App\Controller\Exercice\Front;

use App\Entity\Exercice\Exercise;
use App\Entity\Exercice\ExerciseSession;
use App\Repository\Exercice\ExerciseSessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/session', name: 'app_front_session_')]
final class ExerciseSessionController extends AbstractController
{
    #[Route('/start/{id}', name: 'start', methods: ['GET'])]
    public function start(Exercise $exercise, EntityManagerInterface $entityManager): Response
    {
        $session = new ExerciseSession();

        // À remplacer plus tard par l'utilisateur connecté
        $session->setUserId(1);

        $session->setExerciseId($exercise->getId());
        $session->setStatus('en cours');
        $session->setStartedAt(new \DateTime());
        $session->setActiveSeconds(0);
        $session->setLastResumedAt(new \DateTime());

        $entityManager->persist($session);
        $entityManager->flush();

        $this->addFlash('success', 'La session a été démarrée avec succès.');

        return $this->render('exercice/front/session/start.html.twig', [
            'exercise' => $exercise,
            'session' => $session,
        ]);
    }

    #[Route('/history', name: 'history', methods: ['GET'])]
    public function history(ExerciseSessionRepository $exerciseSessionRepository): Response
    {
        return $this->render('exercice/front/session/history.html.twig', [
            'sessions' => $exerciseSessionRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/end/{id}', name: 'end', methods: ['GET'])]
    public function end(ExerciseSession $session, EntityManagerInterface $entityManager): Response
    {
        $session->setStatus('terminée');
        $session->setCompletedAt(new \DateTime());

        $entityManager->flush();

        $this->addFlash('success', 'La session a été terminée.');

        return $this->redirectToRoute('app_front_session_history');
    }
}
