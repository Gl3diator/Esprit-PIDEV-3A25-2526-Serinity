<?php

namespace App\Controller;

use App\Entity\Reves;
use App\Form\ReveType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reve')]
final class ReveController extends AbstractController
{
    // ─── LIST ────────────────────────────────────────────────
    #[Route('/', name: 'app_reve_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $reves = $em->getRepository(Reves::class)->findAll();

        return $this->render('reve/index.html.twig', [
            'reves' => $reves,
        ]);
    }

    // ─── CREATE ──────────────────────────────────────────────
    #[Route('/new', name: 'app_reve_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $reve = new Reves();
        $form = $this->createForm(ReveType::class, $reve);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reve->setCreatedAt(new \DateTime());
            $reve->setUpdatedAt(new \DateTime());
            $em->persist($reve);
            $em->flush();

            $this->addFlash('success', 'Rêve ajouté avec succès !');
            return $this->redirectToRoute('app_reve_index');
        }

        return $this->render('reve/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // ─── SHOW ────────────────────────────────────────────────
    #[Route('/{id}', name: 'app_reve_show', methods: ['GET'])]
    public function show(Reves $reve): Response
    {
        return $this->render('reve/show.html.twig', [
            'reve' => $reve,
        ]);
    }

    // ─── EDIT ────────────────────────────────────────────────
    #[Route('/{id}/edit', name: 'app_reve_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reves $reve, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ReveType::class, $reve);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reve->setUpdatedAt(new \DateTime());
            $em->flush();

            $this->addFlash('success', 'Rêve modifié avec succès !');
            return $this->redirectToRoute('app_reve_index');
        }

        return $this->render('reve/edit.html.twig', [
            'reve' => $reve,
            'form' => $form->createView(),
        ]);
    }

    // ─── DELETE ──────────────────────────────────────────────
    #[Route('/{id}/delete', name: 'app_reve_delete', methods: ['POST'])]
    public function delete(Request $request, Reves $reve, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reve->getId(), $request->request->get('_token'))) {
            $em->remove($reve);
            $em->flush();
            $this->addFlash('success', 'Rêve supprimé.');
        }

        return $this->redirectToRoute('app_reve_index');
    }
}