<?php

namespace App\Controller\Exercice\Front;

use App\Repository\Exercice\ExerciseRepository;
use App\Entity\Exercice\Exercise;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/exercises', name: 'app_front_exercise_')]
class ExerciseController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, ExerciseRepository $exerciseRepository): Response
    {
        $search = trim((string) $request->query->get('q', ''));
        $type = trim((string) $request->query->get('type', ''));
        $levelRaw = $request->query->get('level');
        $sort = trim((string) $request->query->get('sort', 'title_asc'));
        $level = is_numeric($levelRaw) && $levelRaw !== '' ? (int) $levelRaw : null;

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
