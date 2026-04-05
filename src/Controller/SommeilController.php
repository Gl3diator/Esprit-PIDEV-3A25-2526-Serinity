<?php

namespace App\Controller;

use App\Entity\Sommeil;
use App\Form\SommeilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sommeil')]
final class SommeilController extends AbstractController
{
    // ─── LIST ────────────────────────────────────────────────
    #[Route('/', name: 'app_sommeil_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $sommeils = $em->getRepository(Sommeil::class)->findAll();

        return $this->render('sommeil/index.html.twig', [
            'sommeils' => $sommeils,
        ]);
    }

    // ─── CREATE ──────────────────────────────────────────────
    #[Route('/new', name: 'app_sommeil_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $sommeil = new Sommeil();
        $form = $this->createForm(SommeilType::class, $sommeil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sommeil->setCreatedAt(new \DateTime());
            $sommeil->setUpdatedAt(new \DateTime());
            $em->persist($sommeil);
            $em->flush();

            $this->addFlash('success', 'Nuit de sommeil ajoutée avec succès !');
            return $this->redirectToRoute('app_sommeil_index');
        }

        return $this->render('sommeil/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // ─── SHOW ────────────────────────────────────────────────
    #[Route('/{id}', name: 'app_sommeil_show', methods: ['GET'])]
    public function show(Sommeil $sommeil): Response
    {
        return $this->render('sommeil/show.html.twig', [
            'sommeil' => $sommeil,
        ]);
    }

    // ─── EDIT ────────────────────────────────────────────────
    #[Route('/{id}/edit', name: 'app_sommeil_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sommeil $sommeil, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SommeilType::class, $sommeil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sommeil->setUpdatedAt(new \DateTime());
            $em->flush();

            $this->addFlash('success', 'Nuit de sommeil modifiée avec succès !');
            return $this->redirectToRoute('app_sommeil_index');
        }

        return $this->render('sommeil/edit.html.twig', [
            'sommeil' => $sommeil,
            'form'    => $form->createView(),
        ]);
    }

    // ─── DELETE ──────────────────────────────────────────────
    #[Route('/{id}/delete', name: 'app_sommeil_delete', methods: ['POST'])]
    public function delete(Request $request, Sommeil $sommeil, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $sommeil->getId(), $request->request->get('_token'))) {
            $em->remove($sommeil);
            $em->flush();
            $this->addFlash('success', 'Nuit de sommeil supprimée.');
        }

        return $this->redirectToRoute('app_sommeil_index');
    }
}