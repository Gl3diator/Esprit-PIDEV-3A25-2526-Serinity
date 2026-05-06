<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Entity\ExerciceControl;
use App\Entity\ExerciceFavorite;
use App\Entity\User;
use App\Repository\ExerciceControlRepository;
use App\Repository\ExerciceFavoriteRepository;

final readonly class ExerciseProfileDatasetBuilder
{
    /**
     * @var list<string>
     */
    public const FEATURE_COLUMNS = [
        'total_sessions',
        'completed_sessions',
        'completion_rate',
        'favorite_count',
        'favorite_rate',
        'avg_duration_minutes',
        'avg_active_seconds',
        'avg_engagement_ratio',
        'calm_type_ratio',
        'balanced_type_ratio',
        'active_type_ratio',
        'feedback_positive_score',
        'feedback_present_rate',
    ];
  

    public function __construct(
        private ExerciceControlRepository $controlRepository,
        private ExerciceFavoriteRepository $favoriteRepository,
    ) {
    }

    /**
     * @return list<string>
     */
    public function featureColumns(): array
    {
        return self::FEATURE_COLUMNS;
    }

    /**
     * @param iterable<User> $users
     * @return list<array<string, float|int|string>>
     */
    public function buildDatasetRows(iterable $users): array
    {
        $rows = [];
        foreach ($users as $user) {
            $row = $this->buildDatasetRowForUser($user);
            if ((int) ($row['total_sessions'] ?? 0) <= 0) {
                continue;
            }

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @return array<string, float|int|string>
     */
    public function buildDatasetRowForUser(User $user): array
    {
        $features = $this->buildFeaturePayload($user);

        return [
            // Keep the identifier for CSV/debug reference only. Training and
            // prediction use FEATURE_COLUMNS, which excludes user_id.
            'user_id' => $user->getId(),
            ...$features,
            'target_label' => $this->inferTargetLabel($features),
        ];
    }

    /**
     * @return array<string, float|int>
     */
    public function buildFeaturePayload(User $user): array
    {
        $controls = $this->controlRepository->findAssignedForUser($user);
        $favorites = $this->favoriteRepository->findForUser($user);

        $totalSessions = count($controls);
        $completedSessions = 0;
        $favoriteCount = 0;
        $totalDurationMinutes = 0.0;
        $totalActiveSeconds = 0.0;
        $engagementRatios = [];
        $calmCount = 0;
        $balancedCount = 0;
        $activeCount = 0;
        $feedbackPresentCount = 0;
        $feedbackPositiveTotal = 0.0;
        $uniqueExerciseIds = [];

        foreach ($favorites as $favorite) {
            if ($favorite->getFavoriteType() === ExerciceFavorite::TYPE_EXERCICE) {
                ++$favoriteCount;
            }
        }

        foreach ($controls as $control) {
            $exercise = $control->getExercice();
            $durationMinutes = max(0, $exercise->getDurationMinutes());
            $activeSeconds = max(0, $control->getActiveSeconds());
            $status = strtoupper($control->getStatus());
            $feedback = trim((string) ($control->getFeedback() ?? ''));

            if ($status === ExerciceControl::STATUS_COMPLETED) {
                ++$completedSessions;
            }

            $exerciseId = $exercise->getId();
            if ($exerciseId !== null) {
                $uniqueExerciseIds[(string) $exerciseId] = true;
            }

            $totalDurationMinutes += $durationMinutes;
            $totalActiveSeconds += $activeSeconds;
            $engagementRatios[] = $this->engagementRatio($durationMinutes, $activeSeconds);

            match ($this->classifySessionStyle($control)) {
                'calm' => ++$calmCount,
                'active' => ++$activeCount,
                default => ++$balancedCount,
            };

            if ($feedback !== '') {
                ++$feedbackPresentCount;
                $feedbackPositiveTotal += $this->feedbackPositiveScore($feedback);
            }
        }

        $favoriteBase = max(1, count($uniqueExerciseIds), $totalSessions);
        $sessionBase = max(1, $totalSessions);

        return [
            'total_sessions' => $totalSessions,
            'completed_sessions' => $completedSessions,
            'completion_rate' => round($completedSessions / $sessionBase, 4),
            'favorite_count' => $favoriteCount,
            'favorite_rate' => round(min(1, $favoriteCount / $favoriteBase), 4),
            'avg_duration_minutes' => round($totalDurationMinutes / $sessionBase, 4),
            'avg_active_seconds' => round($totalActiveSeconds / $sessionBase, 4),
            'avg_engagement_ratio' => round(array_sum($engagementRatios) / $sessionBase, 4),
            'calm_type_ratio' => round($calmCount / $sessionBase, 4),
            'balanced_type_ratio' => round($balancedCount / $sessionBase, 4),
            'active_type_ratio' => round($activeCount / $sessionBase, 4),
            'feedback_positive_score' => round($feedbackPresentCount > 0 ? $feedbackPositiveTotal / $feedbackPresentCount : 0, 4),
            'feedback_present_rate' => round($feedbackPresentCount / $sessionBase, 4),
        ];
    }

    /**
     * @param array<string, float|int> $features
     */
    public function inferTargetLabel(array $features): string
    {
        $totalSessions = (int) ($features['total_sessions'] ?? 0);
        $completedSessions = (int) ($features['completed_sessions'] ?? 0);
        $completionRate = (float) ($features['completion_rate'] ?? 0.0);
        $avgDurationMinutes = (float) ($features['avg_duration_minutes'] ?? 0.0);
        $avgEngagementRatio = (float) ($features['avg_engagement_ratio'] ?? 0.0);
        $calmTypeRatio = (float) ($features['calm_type_ratio'] ?? 0.0);
        $activeTypeRatio = (float) ($features['active_type_ratio'] ?? 0.0);
        $feedbackPositiveScore = (float) ($features['feedback_positive_score'] ?? 0.0);

        if ($totalSessions < 3) {
            return 'balanced';
        }

        if (
            $calmTypeRatio >= 0.5
            && $avgDurationMinutes <= 12
            && $avgEngagementRatio <= 0.9
        ) {
            return 'calm';
        }

        if (
            $activeTypeRatio >= 0.45
            && $avgDurationMinutes >= 12
            && $avgEngagementRatio >= 0.75
            && $completionRate >= 0.45
            && $completedSessions >= 2
        ) {
            return 'active';
        }

        if ($feedbackPositiveScore >= 0.7 && $avgEngagementRatio >= 0.85 && $completionRate >= 0.6) {
            return 'active';
        }

        return 'balanced';
    }

    /**
     * @param array<string, float|int> $features
     */
    public function hasEnoughData(array $features): bool
    {
        return (int) ($features['total_sessions'] ?? 0) >= 3;
    }

    private function classifySessionStyle(ExerciceControl $control): string
    {
        $exercise = $control->getExercice();
        $typeBucket = $this->bucketExerciseType($exercise->getType());
        if ($typeBucket !== 'balanced') {
            return $typeBucket;
        }

        $durationMinutes = max(0, $exercise->getDurationMinutes());
        $engagementRatio = $this->engagementRatio($durationMinutes, $control->getActiveSeconds());
        $level = max(1, $exercise->getLevel());

        if ($level <= 1 && $durationMinutes <= 10 && $engagementRatio <= 0.9) {
            return 'calm';
        }

        if ($level >= 3 || $durationMinutes >= 18 || $engagementRatio >= 1.0) {
            return 'active';
        }

        return 'balanced';
    }

    private function bucketExerciseType(string $type): string
    {
        $normalized = mb_strtolower(trim($type));

        foreach (['respiration', 'breath', 'breathing', 'relax', 'calm', 'stretch', 'yoga', 'sleep', 'body scan'] as $keyword) {
            if (str_contains($normalized, $keyword)) {
                return 'calm';
            }
        }

        foreach (['cardio', 'strength', 'power', 'hiit', 'energy', 'energ', 'dynamic', 'endurance'] as $keyword) {
            if (str_contains($normalized, $keyword)) {
                return 'active';
            }
        }

        return 'balanced';
    }

    private function engagementRatio(int $durationMinutes, int $activeSeconds): float
    {
        $plannedSeconds = max(1, $durationMinutes * 60);

        return min(1.5, max(0.0, $activeSeconds / $plannedSeconds));
    }

    private function feedbackPositiveScore(string $feedback): float
    {
        $normalized = mb_strtolower(trim($feedback));
        if ($normalized === '') {
            return 0.0;
        }

        $score = 0.5;

        foreach (['good', 'great', 'better', 'calm', 'relaxed', 'focus', 'focused', 'helpful', 'useful', 'love', 'liked', 'nice', 'energized'] as $positiveKeyword) {
            if (str_contains($normalized, $positiveKeyword)) {
                $score += 0.1;
            }
        }

        foreach (['bad', 'hard', 'stress', 'tired', 'boring', 'pain', 'difficult', 'worse'] as $negativeKeyword) {
            if (str_contains($normalized, $negativeKeyword)) {
                $score -= 0.1;
            }
        }

        return max(0.0, min(1.0, $score));
    }
}
