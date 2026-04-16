<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Access\User;
use App\Entity\Exercice\ExerciseSession;
use App\Repository\Exercice\ExerciseSessionRepository;
use Doctrine\ORM\EntityManagerInterface;

final class PerformanceAnalysisService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function analyze(User $user): array
    {
        $sessions = $this->getSessionsForUser($user);

        $totalSessions = count($sessions);
        $completedSessions = $this->countCompletedSessions($sessions);
        $totalActiveSeconds = $this->sumActiveSeconds($sessions);

        return [
            'totalSessions' => $totalSessions,
            'completedSessions' => $completedSessions,
            'completionRate' => $totalSessions > 0 ? round(($completedSessions / $totalSessions) * 100, 1) : 0,
            'averageDuration' => $completedSessions > 0 ? (int) ($totalActiveSeconds / $completedSessions) : 0,
            'totalMinutes' => (int) ($totalActiveSeconds / 60),
            'totalTimeSpent' => $totalActiveSeconds,
        ];
    }

    /**
     * @return list<ExerciseSession>
     */
    private function getSessionsForUser(User $user): array
    {
        return $this->entityManager
            ->getRepository(ExerciseSession::class)
            ->createQueryBuilder('s')
            ->andWhere('s.user = :user')
            ->setParameter('user', $user)
            ->orderBy('s.startedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param list<ExerciseSession> $sessions
     */
    private function countCompletedSessions(array $sessions): int
    {
        return count(array_filter($sessions, static fn(ExerciseSession $session) => $session->getStatus() === 'terminee'));
    }

    /**
     * @param list<ExerciseSession> $sessions
     */
    private function sumActiveSeconds(array $sessions): int
    {
        return array_reduce($sessions, static fn(int $sum, ExerciseSession $session) => $sum + $session->getActiveSeconds(), 0);
    }
}