<?php

namespace App\Controller;

use App\Entity\MoodEntry;
use App\Form\MoodEntryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/mood/entry')]
final class MoodEntryController extends AbstractController
{
    private const TEMP_USER_ID = '6affa2df-dda9-442d-99ee-d2a3c1e78c64';

    #[Route(name: 'app_mood_entry_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $search = trim((string) $request->query->get('q', ''));
        $selectedType = $request->query->get('type');

        $qb = $entityManager->getRepository(MoodEntry::class)->createQueryBuilder('m')
            ->leftJoin('m.emotions', 'searchEmotion')
            ->leftJoin('m.influences', 'searchInfluence')
            ->andWhere('m.userId = :userId')
            ->setParameter('userId', self::TEMP_USER_ID)
            ->orderBy('m.entryDate', 'DESC')
            ->distinct();

        if (\in_array($selectedType, ['DAY', 'MOMENT'], true)) {
            $qb->andWhere('m.momentType = :momentType')
                ->setParameter('momentType', $selectedType);
        } else {
            $selectedType = '';
        }

        if ($search !== '') {
            $qb->andWhere(
                'LOWER(m.momentType) LIKE :search
                 OR LOWER(searchEmotion.name) LIKE :search
                 OR LOWER(searchInfluence.name) LIKE :search'
            )->setParameter('search', '%'.mb_strtolower($search).'%');
        }

        $moodEntries = $qb->getQuery()->getResult();

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
            'search' => $search,
            'selected_type' => $selectedType,
        ]);
    }

    #[Route('/summary', name: 'app_mood_entry_summary', methods: ['GET'])]
    public function summary(EntityManagerInterface $entityManager): Response
    {
        $weekStart = (new \DateTimeImmutable('today'))->modify('-6 days')->setTime(0, 0, 0);
        $weekEnd = (new \DateTimeImmutable('today'))->setTime(23, 59, 59);

        $weeklyEntries = $entityManager->getRepository(MoodEntry::class)
            ->createQueryBuilder('m')
            ->andWhere('m.userId = :userId')
            ->andWhere('m.entryDate BETWEEN :weekStart AND :weekEnd')
            ->setParameter('userId', self::TEMP_USER_ID)
            ->setParameter('weekStart', $weekStart)
            ->setParameter('weekEnd', $weekEnd)
            ->orderBy('m.entryDate', 'DESC')
            ->getQuery()
            ->getResult();

        $weeklyCount = \count($weeklyEntries);
        $weeklyAverageMood = null;
        $mostUsedType = 'No data';

        if ($weeklyCount > 0) {
            $totalMood = 0;
            $typeCounts = [
                'DAY' => 0,
                'MOMENT' => 0,
            ];

            foreach ($weeklyEntries as $entry) {
                $totalMood += $entry->getMoodLevel();

                if (isset($typeCounts[$entry->getMomentType()])) {
                    $typeCounts[$entry->getMomentType()]++;
                }
            }

            $weeklyAverageMood = round($totalMood / $weeklyCount, 1);
            $mostUsedType = $typeCounts['DAY'] >= $typeCounts['MOMENT'] ? 'DAY' : 'MOMENT';
        }

        $topEmotionRow = $entityManager->getRepository(MoodEntry::class)
            ->createQueryBuilder('m')
            ->select('e.name AS name, COUNT(e.id) AS usageCount')
            ->join('m.emotions', 'e')
            ->andWhere('m.userId = :userId')
            ->andWhere('m.entryDate BETWEEN :weekStart AND :weekEnd')
            ->setParameter('userId', self::TEMP_USER_ID)
            ->setParameter('weekStart', $weekStart)
            ->setParameter('weekEnd', $weekEnd)
            ->groupBy('e.id, e.name')
            ->orderBy('usageCount', 'DESC')
            ->addOrderBy('e.name', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getArrayResult();

        $topInfluenceRow = $entityManager->getRepository(MoodEntry::class)
            ->createQueryBuilder('m')
            ->select('i.name AS name, COUNT(i.id) AS usageCount')
            ->join('m.influences', 'i')
            ->andWhere('m.userId = :userId')
            ->andWhere('m.entryDate BETWEEN :weekStart AND :weekEnd')
            ->setParameter('userId', self::TEMP_USER_ID)
            ->setParameter('weekStart', $weekStart)
            ->setParameter('weekEnd', $weekEnd)
            ->groupBy('i.id, i.name')
            ->orderBy('usageCount', 'DESC')
            ->addOrderBy('i.name', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getArrayResult();

        $topEmotionName = $topEmotionRow[0]['name'] ?? 'No data';
        $topEmotionCount = isset($topEmotionRow[0]['usageCount']) ? (int) $topEmotionRow[0]['usageCount'] : 0;

        $topInfluenceName = $topInfluenceRow[0]['name'] ?? 'No data';
        $topInfluenceCount = isset($topInfluenceRow[0]['usageCount']) ? (int) $topInfluenceRow[0]['usageCount'] : 0;

        return $this->render('mood_entry/summary.html.twig', [
            'weekly_count' => $weeklyCount,
            'weekly_average_mood' => $weeklyAverageMood,
            'most_used_type' => $mostUsedType,
            'top_emotion_name' => $topEmotionName,
            'top_emotion_count' => $topEmotionCount,
            'top_influence_name' => $topInfluenceName,
            'top_influence_count' => $topInfluenceCount,
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

            if ($this->hasDuplicateDayEntry($entityManager, $moodEntry)) {
                $form->addError(new FormError('Only one DAY entry is allowed per day.'));
            } else {
                $entityManager->persist($moodEntry);
                $entityManager->flush();

                return $this->redirectToRoute('app_mood_entry_index', [], Response::HTTP_SEE_OTHER);
            }
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

            if ($this->hasDuplicateDayEntry($entityManager, $moodEntry)) {
                $form->addError(new FormError('Only one DAY entry is allowed per day.'));
            } else {
                $entityManager->flush();

                return $this->redirectToRoute('app_mood_entry_index', [], Response::HTTP_SEE_OTHER);
            }
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

    private function hasDuplicateDayEntry(EntityManagerInterface $entityManager, MoodEntry $moodEntry): bool
    {
        if ($moodEntry->getMomentType() !== 'DAY') {
            return false;
        }

        $entryDate = \DateTimeImmutable::createFromInterface($moodEntry->getEntryDate());
        $startOfDay = $entryDate->setTime(0, 0, 0);
        $endOfDay = $entryDate->setTime(23, 59, 59);

        $qb = $entityManager->getRepository(MoodEntry::class)->createQueryBuilder('m');

        $qb->select('COUNT(m.id)')
            ->andWhere('m.userId = :userId')
            ->andWhere('m.momentType = :momentType')
            ->andWhere('m.entryDate BETWEEN :startOfDay AND :endOfDay')
            ->setParameter('userId', $moodEntry->getUserId())
            ->setParameter('momentType', 'DAY')
            ->setParameter('startOfDay', $startOfDay)
            ->setParameter('endOfDay', $endOfDay);

        if ($moodEntry->getId() !== null) {
            $qb->andWhere('m.id != :currentId')
                ->setParameter('currentId', $moodEntry->getId());
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }
}