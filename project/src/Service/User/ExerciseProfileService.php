<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Dto\Common\ServiceResult;
use App\Entity\User;

final readonly class ExerciseProfileService
{
    public function __construct(
        private ExerciseProfileDatasetBuilder $datasetBuilder,
        private ExerciseProfileApiClient $apiClient,
    ) {
    }

    public function classify(User $user): ServiceResult
    {
        $features = $this->datasetBuilder->buildFeaturePayload($user);

        if (!$this->datasetBuilder->hasEnoughData($features)) {
            return ServiceResult::success('Not enough exercise history yet.', [
                'profile' => 'not_enough_data',
                'displayLabel' => 'Not enough data',
                'explanation' => 'Complete a few more exercise sessions to generate your profile.',
                'source' => 'insufficient_data',
                'features' => $features,
            ]);
        }

        $prediction = $this->apiClient->predict($features);
        if ($prediction === null) {
            return ServiceResult::success('ML API unavailable, fallback profile applied.', [
                'profile' => 'balanced',
                'displayLabel' => 'Balanced',
                'explanation' => $this->explanationForProfile('balanced'),
                'source' => 'fallback',
                'features' => $features,
            ]);
        }

        $profile = $this->normalizeProfile((string) ($prediction['profile'] ?? 'balanced'));

        return ServiceResult::success('Exercise profile predicted successfully.', [
            'profile' => $profile,
            'displayLabel' => $this->displayLabel($profile),
            'explanation' => $this->explanationForProfile($profile),
            'source' => 'ml',
            'probabilities' => $prediction['probabilities'] ?? [],
            'features' => $features,
        ]);
    }

    private function normalizeProfile(string $profile): string
    {
        return match (strtolower(trim($profile))) {
            'calm' => 'calm',
            'active' => 'active',
            'balanced' => 'balanced',
            default => 'balanced',
        };
    }

    private function displayLabel(string $profile): string
    {
        return match ($profile) {
            'calm' => 'Calm',
            'active' => 'Active',
            'balanced' => 'Balanced',
            default => 'Not enough data',
        };
    }

    private function explanationForProfile(string $profile): string
    {
        return match ($profile) {
            'calm' => 'Relaxing exercises suit you best.',
            'active' => 'Longer or more intense exercises suit you best.',
            'balanced' => 'You have a mixed exercise style.',
            default => 'Complete more sessions to learn which exercises suit you best.',
        };
    }
}
