<?php

namespace App\Controller\Mood;

use App\Entity\Mood\JournalEntry;
use App\Form\Mood\JournalEntryType;
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
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $search = trim((string) $request->query->get('q', ''));

        $qb = $entityManager->getRepository(JournalEntry::class)->createQueryBuilder('j')
            ->andWhere('j.userId = :userId')
            ->setParameter('userId', self::TEMP_USER_ID)
            ->orderBy('j.createdAt', 'DESC');

        if ($search !== '') {
            $qb->andWhere('LOWER(j.title) LIKE :search OR LOWER(j.content) LIKE :search')
                ->setParameter('search', '%'.mb_strtolower($search).'%');
        }

        $journalEntries = $qb->getQuery()->getResult();

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

        $allEntries = $entityManager->getRepository(JournalEntry::class)->createQueryBuilder('j')
            ->andWhere('j.userId = :userId')
            ->setParameter('userId', self::TEMP_USER_ID)
            ->orderBy('j.createdAt', 'ASC')
            ->getQuery()
            ->getResult();

        $distinctDays = $this->extractDistinctJournalDays($allEntries);

        $currentStreak = $this->calculateCurrentStreak($distinctDays);
        $longestStreak = $this->calculateLongestStreak($distinctDays);
        $entriesPerDay = [];

foreach ($allEntries as $entry) {
    $createdAt = $entry->getCreatedAt();

    if (!$createdAt instanceof \DateTimeInterface) {
        continue;
    }

    $dayKey = $createdAt->format('Y-m-d');

    if (!isset($entriesPerDay[$dayKey])) {
        $entriesPerDay[$dayKey] = 0;
    }

    $entriesPerDay[$dayKey]++;
}

$maxEntriesOneDay = $entriesPerDay === [] ? 0 : max($entriesPerDay);

        return $this->render('mood/journal_entry/index.html.twig', [
            'grouped_entries' => $groupedEntries,
            'search' => $search,
            'current_streak' => $currentStreak,
            'longest_streak' => $longestStreak,
            'max_entries_one_day' => $maxEntriesOneDay,
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

        return $this->render('mood/journal_entry/new.html.twig', [
            'journal_entry' => $journalEntry,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_journal_entry_show', methods: ['GET'])]
    public function show(JournalEntry $journalEntry): Response
    {
        return $this->render('mood/journal_entry/show.html.twig', [
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

        return $this->render('mood/journal_entry/edit.html.twig', [
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

    /**
     * @param JournalEntry[] $entries
     * @return string[]
     */
    private function extractDistinctJournalDays(array $entries): array
    {
        $days = [];

        foreach ($entries as $entry) {
            $createdAt = $entry->getCreatedAt();

            if (!$createdAt instanceof \DateTimeInterface) {
                continue;
            }

            $days[$createdAt->format('Y-m-d')] = true;
        }

        $distinctDays = array_keys($days);
        sort($distinctDays);

        return $distinctDays;
    }

    /**
     * @param string[] $distinctDays
     */
    private function calculateCurrentStreak(array $distinctDays): int
    {
        if ($distinctDays === []) {
            return 0;
        }

        $daySet = array_flip($distinctDays);
        $cursor = new \DateTimeImmutable('today');
        $streak = 0;

        while (isset($daySet[$cursor->format('Y-m-d')])) {
            $streak++;
            $cursor = $cursor->modify('-1 day');
        }

        return $streak;
    }

    /**
     * @param string[] $distinctDays
     */
    private function calculateLongestStreak(array $distinctDays): int
    {
        if ($distinctDays === []) {
            return 0;
        }

        $longest = 1;
        $current = 1;

        for ($i = 1; $i < count($distinctDays); $i++) {
            $previousDay = new \DateTimeImmutable($distinctDays[$i - 1]);
            $currentDay = new \DateTimeImmutable($distinctDays[$i]);

            if ($previousDay->modify('+1 day')->format('Y-m-d') === $currentDay->format('Y-m-d')) {
                $current++;
            } else {
                $current = 1;
            }

            if ($current > $longest) {
                $longest = $current;
            }
        }

        return $longest;
    }
}
