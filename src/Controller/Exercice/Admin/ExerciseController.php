<?php

namespace App\Controller\Exercice\Admin;

use App\Entity\Exercice\Exercise;
use App\Form\Exercice\ExerciseType;
use App\Repository\Exercice\ExerciseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/exercise', name: 'app_admin_exercise_')]
final class ExerciseController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request, ExerciseRepository $exerciseRepository): Response
    {
        $search = trim((string) $request->query->get('q', ''));
        $type = trim((string) $request->query->get('type', ''));
        $levelRaw = $request->query->get('level');
        $sort = trim((string) $request->query->get('sort', 'title_asc'));
        $level = is_numeric($levelRaw) && $levelRaw !== '' ? (int) $levelRaw : null;

        return $this->render('exercice/admin/exercise/index.html.twig', [
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

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $exercise = new Exercise();
        $form = $this->createForm(ExerciseType::class, $exercise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($exercise);
            $entityManager->flush();

            $this->addFlash('success', 'L exercice a ete cree avec succes.');

            return $this->redirectToRoute('app_admin_exercise_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted()) {
            $this->addFlash('error', 'Le formulaire contient des erreurs. Merci de verifier les champs saisis.');
        }

        return $this->render('exercice/admin/exercise/new.html.twig', [
            'exercise' => $exercise,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Exercise $exercise): Response
    {
        return $this->render('exercice/admin/exercise/show.html.twig', [
            'exercise' => $exercise,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Exercise $exercise, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ExerciseType::class, $exercise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'L exercice a ete mis a jour avec succes.');

            return $this->redirectToRoute('app_admin_exercise_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted()) {
            $this->addFlash('error', 'Le formulaire contient des erreurs. Merci de verifier les champs saisis.');
        }

        return $this->render('exercice/admin/exercise/edit.html.twig', [
            'exercise' => $exercise,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Exercise $exercise, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $exercise->getId(), $request->request->get('_token'))) {
            $entityManager->remove($exercise);
            $entityManager->flush();
            $this->addFlash('success', 'L exercice a ete supprime avec succes.');
        } else {
            $this->addFlash('error', 'La suppression de l exercice a echoue.');
        }

        return $this->redirectToRoute('app_admin_exercise_index', [], Response::HTTP_SEE_OTHER);
    }
}
