<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Access\User;
use App\Entity\Exercice\ExerciseSession;
use Doctrine\ORM\EntityManagerInterface;

final class GoalService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function calculateGoal(User $user, string $goalType, int $targetValue): array
    {
        $currentValue = match ($goalType) {
            'sessions_per_week' => $this->countSessionsThisWeek($user),
            'minutes_per_day' => $this->sumMinutesToday($user),
            'minutes_per_month' => $this->sumMinutesThisMonth($user),
            default => 0,
        };

        $progressPercent = $targetValue > 0
            ? min(100, round(($currentValue / $targetValue) * 100, 1))
            : 0;

        $status = $this->determineStatus($currentValue, $progressPercent, $targetValue);

        return [
            'goalType' => $goalType,
            'targetValue' => $targetValue,
            'currentValue' => $currentValue,
            'progressPercent' => $progressPercent,
            'status' => $status,
        ];
    }

    private function countSessionsThisWeek(User $user): int
    {
        $startOfWeek = new \DateTime('monday this week 00:00:00');
        $endOfWeek = new \DateTime('sunday this week 23:59:59');

        return $this->countCompletedSessionsBetween($user, $startOfWeek, $endOfWeek);
    }

    private function sumMinutesToday(User $user): int
    {
        $startOfDay = new \DateTime('today 00:00:00');
        $endOfDay = new \DateTime('today 23:59:59');

        return $this->sumActiveSecondsBetween($user, $startOfDay, $endOfDay) / 60;
    }

    private function sumMinutesThisMonth(User $user): int
    {
        $startOfMonth = new \DateTime('first day of this month 00:00:00');
        $endOfMonth = new \DateTime('last day of this month 23:59:59');

        return $this->sumActiveSecondsBetween($user, $startOfMonth, $endOfMonth) / 60;
    }

    private function countCompletedSessionsBetween(User $user, \DateTime $start, \DateTime $end): int
    {
        return count($this->getSessionsBetween($user, $start, $end, 'terminee'));
    }

    private function sumActiveSecondsBetween(User $user, \DateTime $start, \DateTime $end): int
    {
        $sessions = $this->getSessionsBetween($user, $start, $end, null);

        return array_reduce(
            $sessions,
            static fn(int $sum, ExerciseSession $session) => $sum + $session->getActiveSeconds(),
            0
        );
    }

    /**
     * @return list<ExerciseSession>
     */
    private function getSessionsBetween(User $user, \DateTime $start, \DateTime $end, ?string $status): array
    {
        $qb = $this->entityManager
            ->getRepository(ExerciseSession::class)
            ->createQueryBuilder('s')
            ->andWhere('s.user = :user')
            ->andWhere('s.startedAt >= :start')
            ->andWhere('s.startedAt <= :end')
            ->setParameter('user', $user)
            ->setParameter('start', $start)
            ->setParameter('end', $end);

        if ($status !== null) {
            $qb->andWhere('s.status = :status')
                ->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }

    private function determineStatus(int $currentValue, float $progressPercent, int $targetValue): string
    {
        if ($currentValue === 0 && $targetValue > 0) {
            return 'NOT_STARTED';
        }

        if ($progressPercent >= 100) {
            return 'COMPLETED';
        }

        return 'IN_PROGRESS';
    }
}