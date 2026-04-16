<?php

declare(strict_types=1);

namespace App\Controller\Access\User;

use App\Service\User\UserDashboardService;
use App\Service\PerformanceAnalysisService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
final class DashboardController extends AbstractUserUiController
{
    public function __construct(
        private readonly UserDashboardService $userDashboardService,
        private readonly PerformanceAnalysisService $performanceAnalysisService,
    ) {
    }

    #[Route('/dashboard', name: 'user_ui_dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        $user = $this->getUser();
        $isUser = $user instanceof \App\Entity\Access\User
            && in_array($user->getRole(), ['PATIENT', 'THERAPIST'], true);

        return $this->render('access/user/pages/dashboard.html.twig', [
            'nav'      => $this->buildNav('user_ui_dashboard'),
            // TEMP DEV MODE: allow dashboard access without a fully authenticated session.
            'userName' => $isUser ? $user->getEmail() : 'Development User',
            'summary'  => $isUser ? $this->userDashboardService->getSummary($user) : [
                'activeSessions' => 0,
                'recentAuditEvents' => 0,
                'profileCompletion' => 0,
                'role' => 'GUEST',
                'accountStatus' => 'DEV_MODE',
            ],
            'exerciseStats' => $isUser ? $this->performanceAnalysisService->analyze($user) : [
                'totalSessions' => 0,
                'completedSessions' => 0,
                'completionRate' => 0,
                'averageDuration' => 0,
                'totalTimeSpent' => 0,
            ],
        ]);
    }

    #[Route('/consultations', name: 'user_ui_consultations', methods: ['GET'])]
    public function consultations(): Response
    {
        $user = $this->currentUser();

        return $this->render('access/access_control/pages/coming_soon.html.twig', [
            'nav'      => $this->buildNav('user_ui_consultations'),
            'userName' => $user->getEmail(),
            'title'    => 'Consultations',
            'subtitle' => 'User consultations module will be available soon.',
        ]);
    }

    #[Route('/user/exercises', name: 'user_ui_exercises', methods: ['GET'])]
    public function exercises(): Response
    {
        return $this->redirectToRoute('app_front_exercise_index');
    }

    #[Route('/forum', name: 'user_ui_forum', methods: ['GET'])]
    public function forum(): Response
    {
        $user = $this->currentUser();

        return $this->render('access/access_control/pages/coming_soon.html.twig', [
            'nav'      => $this->buildNav('user_ui_forum'),
            'userName' => $user->getEmail(),
            'title'    => 'Forum',
            'subtitle' => 'User forum module will be available soon.',
        ]);
    }

    #[Route('/mood', name: 'user_ui_mood', methods: ['GET'])]
    public function mood(): Response
    {
        $user = $this->currentUser();

        return $this->render('access/access_control/pages/coming_soon.html.twig', [
            'nav'      => $this->buildNav('user_ui_mood'),
            'userName' => $user->getEmail(),
            'title'    => 'Mood',
            'subtitle' => 'Mood tracking module will be available soon.',
        ]);
    }

    #[Route('/sleep', name: 'user_ui_sleep', methods: ['GET'])]
    public function sleep(): Response
    {
        $user = $this->currentUser();

        return $this->render('access/access_control/pages/coming_soon.html.twig', [
            'nav'      => $this->buildNav('user_ui_sleep'),
            'userName' => $user->getEmail(),
            'title'    => 'Sleep',
            'subtitle' => 'Sleep tracking module will be available soon.',
        ]);
    }
}
