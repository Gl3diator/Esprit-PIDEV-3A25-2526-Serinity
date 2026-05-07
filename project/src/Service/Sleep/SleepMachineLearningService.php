<?php

namespace App\Service\Sleep;

use Symfony\Component\Process\Process;

final class SleepMachineLearningService
{
    /**
     * @return array<string, mixed>
     */
    public function predictSleepQuality(
        float $dureeSommeil,
        int $interruptions,
        float $temperature,
        int $bruitNiveau,
        string $humeurReveil
    ): array {
        $projectDir = dirname(__DIR__, 3);
        $scriptPath = $projectDir . DIRECTORY_SEPARATOR . 'ml' . DIRECTORY_SEPARATOR . 'predict_sleep.py';

        $venvPython = $projectDir . DIRECTORY_SEPARATOR . '.venv' . DIRECTORY_SEPARATOR . 'Scripts' . DIRECTORY_SEPARATOR . 'python.exe';
        $pythonExecutable = file_exists($venvPython) ? $venvPython : 'python';

        $env = null;

        if (\DIRECTORY_SEPARATOR === '\\') {
            $systemRoot = getenv('SystemRoot');
            if (!is_string($systemRoot) || $systemRoot === '') {
                $systemRoot = 'C:\\Windows';
            }

            $comSpec = getenv('ComSpec');
            if (!is_string($comSpec) || $comSpec === '') {
                $comSpec = 'C:\\Windows\\System32\\cmd.exe';
            }

            $env = [
                'SystemRoot' => $systemRoot,
                'ComSpec' => $comSpec,
                'PATH' => getenv('PATH') ?: '',
                'PATHEXT' => getenv('PATHEXT') ?: '.COM;.EXE;.BAT;.CMD',
                'WINDIR' => getenv('WINDIR') ?: $systemRoot,
                'TEMP' => getenv('TEMP') ?: sys_get_temp_dir(),
                'TMP' => getenv('TMP') ?: sys_get_temp_dir(),
            ];
        }

        $process = new Process([
            $pythonExecutable,
            $scriptPath,
            (string) $dureeSommeil,
            (string) $interruptions,
            (string) $temperature,
            (string) $bruitNiveau,
            $humeurReveil,
        ], $projectDir, $env);

        $process->setTimeout(60);
        $process->run();

        if (!$process->isSuccessful()) {
            return [
                'success' => false,
                'error' => 'Impossible d’exécuter le script Python.',
                'details' => $process->getErrorOutput() ?: $process->getOutput(),
            ];
        }

        $output = $process->getOutput();
        $decoded = json_decode($output, true);

        if (!is_array($decoded)) {
            return [
                'success' => false,
                'error' => 'Réponse Python invalide.',
                'details' => $output,
            ];
        }

        return $decoded;
    }
}