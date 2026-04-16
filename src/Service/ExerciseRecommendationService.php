<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Access\User;
use App\Entity\Exercice\Exercise;
use App\Entity\Exercice\ExerciseSession;
use Doctrine\ORM\EntityManagerInterface;

final class ExerciseRecommendationService
{
    private const CALM_TYPES = [' respiration', 'étirement', 'relaxation', 'yoga', 'méditation'];
    private const ENERGIZING_TYPES = ['cardio', 'renforcement', 'mouvement', 'actif'];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param list<Exercise> $exercises
     * @return list<Exercise>
     */
    public function getRecommendations(User $user, array $exercises, int $limit = 3): array
    {
        if (empty($exercises)) {
            return [];
        }

        $userActivity = $this->estimateUserActivity($user);
        $timeOfDay = $this->getTimeOfDay();
        $scores = [];

        foreach ($exercises as $exercise) {
            $scores[$exercise->getId()] = $this->calculateScore($exercise, $userActivity, $timeOfDay);
        }

        uasort($scores, static fn(int $a, int $b) => $b <=> $a);

        $topIds = array_slice(array_keys($scores), 0, $limit, true);

        return array_values(array_filter($exercises, static fn(Exercise $e) => in_array($e->getId(), $topIds, true)));
    }

    private function estimateUserActivity(User $user): string
    {
        $startOfWeek = new \DateTime('monday this week 00:00:00');
        $endOfWeek = new \DateTime('sunday this week 23:59:59');

        $sessions = $this->entityManager
            ->getRepository(ExerciseSession::class)
            ->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->andWhere('s.user = :user')
            ->andWhere('s.startedAt >= :start')
            ->andWhere('s.startedAt <= :end')
            ->andWhere('s.status = :status')
            ->setParameter('user', $user)
            ->setParameter('start', $startOfWeek)
            ->setParameter('end', $endOfWeek)
            ->setParameter('status', 'terminee')
            ->getQuery()
            ->getSingleScalarResult();

        return match (true) {
            $sessions >= 5 => 'high',
            $sessions >= 2 => 'medium',
            default => 'low',
        };
    }

    private function getTimeOfDay(): string
    {
        $hour = (int) (new \DateTime())->format('H');

        return match (true) {
            $hour < 10 => 'morning',
            $hour < 18 => 'afternoon',
            $hour < 21 => 'evening',
            default => 'night',
        };
    }

    private function calculateScore(Exercise $exercise, string $userActivity, string $timeOfDay): int
    {
        $score = 0;

        $score += $this->scoreByTimeOfDay($exercise, $timeOfDay);
        $score += $this->scoreByUserActivity($exercise, $userActivity);
        $score += $this->scoreByPreferredLevel($exercise);

        return $score;
    }

    private function scoreByTimeOfDay(Exercise $exercise, string $timeOfDay): int
    {
        $type = strtolower($exercise->getType() ?? '');

        return match ($timeOfDay) {
            'morning' => $exercise->getDurationMinutes() <= 15 ? 5 : 0,
            'evening', 'night' => $this->isCalmType($type) ? 10 : 0,
            'afternoon' => $this->isEnergizingType($type) ? 3 : 0,
            default => 0,
        };
    }

    private function scoreByUserActivity(Exercise $exercise, string $userActivity): int
    {
        return match ($userActivity) {
            'low' => $exercise->getDurationMinutes() <= 15 ? 8 : ($exercise->getDurationMinutes() <= 30 ? 4 : 0),
            'medium' => $exercise->getDurationMinutes() <= 30 ? 5 : 2,
            'high' => $exercise->getLevel() <= 5 ? 5 : ($exercise->getLevel() <= 7 ? 3 : 0),
            default => 0,
        };
    }

    private function scoreByPreferredLevel(Exercise $exercise): int
    {
        return match (true) {
            $exercise->getLevel() <= 3 => 5,
            $exercise->getLevel() <= 5 => 3,
            $exercise->getLevel() <= 7 => 1,
            default => 0,
        };
    }

    private function isCalmType(string $type): bool
    {
        foreach (self::CALM_TYPES as $calmType) {
            if (str_contains($type, $calmType)) {
                return true;
            }
        }
        return false;
    }

    private function isEnergizingType(string $type): bool
    {
        foreach (self::ENERGIZING_TYPES as $energyType) {
            if (str_contains($type, $energyType)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param list<Exercise> $exercises
     * @return list<array{exercise: Exercise, timeOfDay: string, weather: string, recommendationReason: string}>
     */
    public function getRecommendationsWithContext(User $user, array $exercises, TimeContextService $timeService, WeatherService $weatherService, int $limit = 3): array
    {
        $recommendations = $this->getRecommendations($user, $exercises, $limit);
        $timeOfDay = $timeService->getTimeOfDay();
        $weather = $weatherService->getCurrentWeather();

        return array_map(function (Exercise $exercise) use ($timeOfDay, $weather) {
            return [
                'exercise' => $exercise,
                'timeOfDay' => $timeOfDay,
                'weather' => $weather,
                'recommendationReason' => $this->generateReason($exercise, $timeOfDay, $weather),
            ];
        }, $recommendations);
    }

    private function generateReason(Exercise $exercise, string $timeOfDay, string $weather): string
    {
        $type = strtolower($exercise->getType() ?? '');
        $reasons = [];

        if ($this->isCalmType($type)) {
            $reasons[] = 'exercice calme';
        }
        if ($exercise->getDurationMinutes() <= 10) {
            $reasons[] = 'exercice court';
        }
        if ($exercise->getLevel() <= 3) {
            $reasons[] = 'adapté aux débutants';
        }

        if ($timeOfDay === 'soir' && $this->isCalmType($type)) {
            return 'exercice calme adapté à une fin de journée';
        }
        if ($timeOfDay === 'matin' && $exercise->getDurationMinutes() <= 15) {
            return 'exercice court pour bien démarrer la journée';
        }
        if ($weather === 'pluvieux' && $this->isCalmType($type)) {
            return 'parfait pour une journée pluvieuse';
        }

        return !empty($reasons) ? implode(' et ', $reasons) : 'adapté à votre niveau';
    }
}