<?php

namespace App\Controller;

use App\Entity\Influence;
use App\Form\InfluenceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/influence')]
final class InfluenceController extends AbstractController
{
    #[Route(name: 'app_influence_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $influences = $entityManager
            ->getRepository(Influence::class)
            ->findAll();

        return $this->render('influence/index.html.twig', [
            'influences' => $influences,
        ]);
    }

    #[Route('/new', name: 'app_influence_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $influence = new Influence();
        $form = $this->createForm(InfluenceType::class, $influence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($influence);
            $entityManager->flush();

            return $this->redirectToRoute('app_influence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('influence/new.html.twig', [
            'influence' => $influence,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_influence_show', methods: ['GET'])]
    public function show(Influence $influence): Response
    {
        return $this->render('influence/show.html.twig', [
            'influence' => $influence,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_influence_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Influence $influence, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InfluenceType::class, $influence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_influence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('influence/edit.html.twig', [
            'influence' => $influence,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_influence_delete', methods: ['POST'])]
    public function delete(Request $request, Influence $influence, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$influence->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($influence);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_influence_index', [], Response::HTTP_SEE_OTHER);
    }
}
