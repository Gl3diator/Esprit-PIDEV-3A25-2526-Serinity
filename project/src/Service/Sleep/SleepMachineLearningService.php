<?php

namespace App\Service\Sleep;

final class SleepMachineLearningService
{
    public function predictSleepQuality(
        float $dureeSommeil,
        int $interruptions,
        float $temperature,
        int $bruitNiveau,
        string $humeurReveil
    ): array {
        $projectDir = dirname(__DIR__, 3);

        $scriptPath = $projectDir . DIRECTORY_SEPARATOR . 'ml' . DIRECTORY_SEPARATOR . 'predict_sleep.py';

        $command = sprintf(
            'py %s %s %s %s %s %s',
            escapeshellarg($scriptPath),
            escapeshellarg((string) $dureeSommeil),
            escapeshellarg((string) $interruptions),
            escapeshellarg((string) $temperature),
            escapeshellarg((string) $bruitNiveau),
            escapeshellarg($humeurReveil)
        );

        $output = shell_exec($command);

        if ($output === null) {
            return [
                'success' => false,
                'error' => 'Impossible d’exécuter le script Python.',
            ];
        }

        $result = json_decode($output, true);

        if (!is_array($result)) {
            return [
                'success' => false,
                'error' => 'Réponse Python invalide.',
                'raw' => $output,
            ];
        }

        return $result;
    }
}