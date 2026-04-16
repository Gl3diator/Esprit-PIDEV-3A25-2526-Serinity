<?php

namespace App\Controller\Exercice\Front;

use App\Repository\Exercice\ExerciseRepository;
use App\Entity\Access\User;
use App\Entity\Exercice\Exercise;
use App\Service\GoalService;
use App\Service\ExerciseRecommendationService;
use App\Service\TimeContextService;
use App\Service\WeatherService;
use App\Service\QuoteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/exercises', name: 'app_front_exercise_')]
class ExerciseController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, ExerciseRepository $exerciseRepository, GoalService $goalService, ExerciseRecommendationService $recommendationService, TimeContextService $timeService, WeatherService $weatherService, QuoteService $quoteService): Response
    {
        $search = trim((string) $request->query->get('q', ''));
        $type = trim((string) $request->query->get('type', ''));
        $levelRaw = $request->query->get('level');
        $sort = trim((string) $request->query->get('sort', 'title_asc'));
        $level = is_numeric($levelRaw) && $levelRaw !== '' ? (int) $levelRaw : null;

        $user = $this->getUser();
        $goal = $user instanceof User
            ? $goalService->calculateGoal($user, 'sessions_per_week', 5)
            : ['currentValue' => 0, 'targetValue' => 5, 'progressPercent' => 0, 'status' => 'NOT_STARTED'];

        $allExercises = $exerciseRepository->findAll();
        $recommendedExercises = $user instanceof User
            ? $recommendationService->getRecommendationsWithContext($user, $allExercises, $timeService, $weatherService, 3)
            : [];

        $quote = $quoteService->getRandomQuote();
        $currentTimeOfDay = $timeService->getTimeOfDay();
        $currentWeather = $weatherService->getCurrentWeather();

        return $this->render('exercice/front/exercise/index.html.twig', [
            'exercises' => $exerciseRepository->findForFront($search, $type, $level, $sort),
            'availableTypes' => $exerciseRepository->findAvailableTypes(),
            'availableLevels' => $exerciseRepository->findAvailableLevels(),
            'filters' => [
                'q' => $search,
                'type' => $type,
                'level' => $level,
                'sort' => $sort,
            ],
            'goal' => $goal,
            'recommendedExercises' => $recommendedExercises,
            'quote' => $quote,
            'currentTimeOfDay' => $currentTimeOfDay,
            'currentWeather' => $currentWeather,
        ]);
    }

    #[Route('/{id}', name: 'show')]
    public function show(Exercise $exercise): Response
    {
        return $this->render('exercice/front/exercise/show.html.twig', [
            'exercise' => $exercise,
        ]);
    }
}
