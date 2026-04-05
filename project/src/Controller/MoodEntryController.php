<?php

namespace App\Controller;

use App\Entity\MoodEntry;
use App\Form\MoodEntryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/mood/entry')]
final class MoodEntryController extends AbstractController
{
    private const TEMP_USER_ID = '6affa2df-dda9-442d-99ee-d2a3c1e78c64';

    #[Route(name: 'app_mood_entry_index', methods: ['GET'])]
public function index(EntityManagerInterface $entityManager): Response
{
    $moodEntries = $entityManager
        ->getRepository(MoodEntry::class)
        ->findBy([], ['entryDate' => 'DESC']);

    $today = new \DateTimeImmutable('today');
    $yesterday = $today->modify('-1 day');

    $groupedEntries = [];

    foreach ($moodEntries as $moodEntry) {
        $entryDate = $moodEntry->getEntryDate();

        if (!$entryDate instanceof \DateTimeInterface) {
            continue;
        }

        $entryDay = \DateTimeImmutable::createFromInterface($entryDate)->setTime(0, 0);

        if ($entryDay == $today) {
            $groupKey = 'today';
            $groupLabel = 'Today';
        } elseif ($entryDay == $yesterday) {
            $groupKey = 'yesterday';
            $groupLabel = 'Yesterday';
        } else {
            $groupKey = $entryDay->format('Y-m-d');
            $groupLabel = $entryDay->format('Y-m-d');
        }

        if (!isset($groupedEntries[$groupKey])) {
            $groupedEntries[$groupKey] = [
                'label' => $groupLabel,
                'entries' => [],
            ];
        }

        $groupedEntries[$groupKey]['entries'][] = $moodEntry;
    }

    return $this->render('mood_entry/index.html.twig', [
        'grouped_entries' => $groupedEntries,
    ]);
}

    #[Route('/new', name: 'app_mood_entry_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $moodEntry = new MoodEntry();
        $moodEntry->setUserId(self::TEMP_USER_ID);
        $moodEntry->setEntryDate(new \DateTime());
        $moodEntry->setUpdatedAt(new \DateTime());

        $form = $this->createForm(MoodEntryType::class, $moodEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $moodEntry->setUpdatedAt(new \DateTime());

            $entityManager->persist($moodEntry);
            $entityManager->flush();

            return $this->redirectToRoute('app_mood_entry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mood_entry/new.html.twig', [
            'mood_entry' => $moodEntry,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_mood_entry_show', methods: ['GET'])]
    public function show(MoodEntry $moodEntry): Response
    {
        return $this->render('mood_entry/show.html.twig', [
            'mood_entry' => $moodEntry,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_mood_entry_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MoodEntry $moodEntry, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MoodEntryType::class, $moodEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $moodEntry->setUpdatedAt(new \DateTime());

            $entityManager->flush();

            return $this->redirectToRoute('app_mood_entry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mood_entry/edit.html.twig', [
            'mood_entry' => $moodEntry,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_mood_entry_delete', methods: ['POST'])]
    public function delete(Request $request, MoodEntry $moodEntry, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$moodEntry->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($moodEntry);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_mood_entry_index', [], Response::HTTP_SEE_OTHER);
    }
}