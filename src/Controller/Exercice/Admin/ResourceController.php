<?php

namespace App\Controller\Exercice\Admin;

use App\Entity\Exercice\Resource;
use App\Form\Exercice\ResourceType;
use App\Repository\Exercice\ExerciseRepository;
use App\Repository\Exercice\ResourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/resource', name: 'app_admin_resource_')]
final class ResourceController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ResourceRepository $resourceRepository): Response
    {
        return $this->render('exercice/admin/resource/index.html.twig', [
            'resources' => $resourceRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, ExerciseRepository $exerciseRepository, EntityManagerInterface $entityManager): Response
    {
        $resource = new Resource();
        $exerciseId = $request->query->get('exercise');

        if (is_numeric($exerciseId)) {
            $exercise = $exerciseRepository->find((int) $exerciseId);
            if ($exercise !== null) {
                $resource->setExercise($exercise);
            }
        }

        $form = $this->createForm(ResourceType::class, $resource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($resource);
            $entityManager->flush();

            $this->addFlash('success', 'La ressource a ete creee avec succes.');

            return $this->redirectToRoute('app_admin_resource_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted()) {
            $this->addFlash('error', 'Le formulaire contient des erreurs. Merci de verifier les champs saisis.');
        }

        return $this->render('exercice/admin/resource/new.html.twig', [
            'resource' => $resource,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Resource $resource): Response
    {
        return $this->render('exercice/admin/resource/show.html.twig', [
            'resource' => $resource,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Resource $resource, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ResourceType::class, $resource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La ressource a ete mise a jour avec succes.');

            return $this->redirectToRoute('app_admin_resource_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted()) {
            $this->addFlash('error', 'Le formulaire contient des erreurs. Merci de verifier les champs saisis.');
        }

        return $this->render('exercice/admin/resource/edit.html.twig', [
            'resource' => $resource,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Resource $resource, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $resource->getId(), $request->request->get('_token'))) {
            $entityManager->remove($resource);
            $entityManager->flush();
            $this->addFlash('success', 'La ressource a ete supprimee avec succes.');
        } else {
            $this->addFlash('error', 'La suppression de la ressource a echoue.');
        }

        return $this->redirectToRoute('app_admin_resource_index', [], Response::HTTP_SEE_OTHER);
    }
}
