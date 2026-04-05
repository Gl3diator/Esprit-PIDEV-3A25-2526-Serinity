<?php

namespace App\Controller;

use App\Entity\JournalEntry;
use App\Form\JournalEntryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/journal/entry')]
final class JournalEntryController extends AbstractController
{
    private const TEMP_USER_ID = '6affa2df-dda9-442d-99ee-d2a3c1e78c64';

    #[Route(name: 'app_journal_entry_index', methods: ['GET'])]
public function index(EntityManagerInterface $entityManager): Response
{
    $journalEntries = $entityManager
        ->getRepository(JournalEntry::class)
        ->findBy([], ['createdAt' => 'DESC']);

    $today = new \DateTimeImmutable('today');
    $yesterday = $today->modify('-1 day');

    $groupedEntries = [];

    foreach ($journalEntries as $journalEntry) {
        $createdAt = $journalEntry->getCreatedAt();

        if (!$createdAt instanceof \DateTimeInterface) {
            continue;
        }

        $entryDay = \DateTimeImmutable::createFromInterface($createdAt)->setTime(0, 0);

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

        $groupedEntries[$groupKey]['entries'][] = $journalEntry;
    }

    return $this->render('journal_entry/index.html.twig', [
        'grouped_entries' => $groupedEntries,
    ]);
}

    #[Route('/new', name: 'app_journal_entry_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $journalEntry = new JournalEntry();
        $journalEntry->setUserId(self::TEMP_USER_ID);
        $journalEntry->setCreatedAt(new \DateTime());
        $journalEntry->setUpdatedAt(new \DateTime());

        $form = $this->createForm(JournalEntryType::class, $journalEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $journalEntry->setUpdatedAt(new \DateTime());

            $entityManager->persist($journalEntry);
            $entityManager->flush();

            return $this->redirectToRoute('app_journal_entry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('journal_entry/new.html.twig', [
            'journal_entry' => $journalEntry,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_journal_entry_show', methods: ['GET'])]
    public function show(JournalEntry $journalEntry): Response
    {
        return $this->render('journal_entry/show.html.twig', [
            'journal_entry' => $journalEntry,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_journal_entry_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, JournalEntry $journalEntry, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(JournalEntryType::class, $journalEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $journalEntry->setUpdatedAt(new \DateTime());

            $entityManager->flush();

            return $this->redirectToRoute('app_journal_entry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('journal_entry/edit.html.twig', [
            'journal_entry' => $journalEntry,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_journal_entry_delete', methods: ['POST'])]
    public function delete(Request $request, JournalEntry $journalEntry, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$journalEntry->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($journalEntry);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_journal_entry_index', [], Response::HTTP_SEE_OTHER);
    }
}