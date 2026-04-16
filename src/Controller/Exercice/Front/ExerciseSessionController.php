<?php

namespace App\Controller\Exercice\Front;

use App\Entity\Access\User;
use App\Entity\Exercice\Exercise;
use App\Entity\Exercice\ExerciseSession;
use App\Repository\Exercice\ExerciseSessionRepository;
use App\Service\PerformanceAnalysisService;
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
        $user = $this->getUser();
        if (!$user instanceof User) {
            $this->addFlash('error', 'Vous devez etre connecte pour demarrer une session d exercice.');

            return $this->redirectToRoute('app_front_exercise_show', ['id' => $exercise->getId()]);
        }

        $session = new ExerciseSession();
        $now = new \DateTime();

        $session->setUser($user);
        $session->setExercise($exercise);
        $session->setStatus('en cours');
        $session->setStartedAt($now);
        $session->setActiveSeconds(0);
        $session->setLastResumedAt($now);

        $entityManager->persist($session);
        $entityManager->flush();

        $this->addFlash('success', 'La session a ete demarree avec succes.');

        return $this->render('exercice/front/session/start.html.twig', [
            'exercise' => $exercise,
            'session' => $session,
        ]);
    }

    #[Route('/history', name: 'history', methods: ['GET'])]
    public function history(ExerciseSessionRepository $exerciseSessionRepository, PerformanceAnalysisService $performanceService): Response
    {
        $user = $this->getUser();
        $stats = $user instanceof User ? $performanceService->analyze($user) : [
            'totalSessions' => 0,
            'completedSessions' => 0,
            'completionRate' => 0,
            'averageDuration' => 0,
            'totalMinutes' => 0,
            'totalTimeSpent' => 0,
        ];

        return $this->render('exercice/front/session/history.html.twig', [
            'sessions' => $exerciseSessionRepository->findBy([], ['id' => 'DESC']),
            'stats' => $stats,
        ]);
    }

    #[Route('/end/{id}', name: 'end', methods: ['GET'])]
    public function end(ExerciseSession $session, EntityManagerInterface $entityManager): Response
    {
        $session->setStatus('terminee');
        $session->setCompletedAt(new \DateTime());

        $entityManager->flush();

        $this->addFlash('success', 'La session a ete terminee.');

        return $this->redirectToRoute('app_front_session_history');
    }
}
