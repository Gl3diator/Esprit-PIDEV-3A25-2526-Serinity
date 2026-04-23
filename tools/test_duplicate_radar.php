<?php

declare(strict_types=1);

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

$projectDir = dirname(__DIR__);
require $projectDir . '/vendor/autoload.php';

if (class_exists(Dotenv::class)) {
    (new Dotenv())->bootEnv($projectDir . '/.env');
}

$env = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? 'dev';
$debug = (bool) ($_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? true);

$kernel = new Kernel($env, $debug);
$kernel->boot();

$container = $kernel->getContainer();

try {
    $doctrine = $container->get('doctrine');
    $repo = $doctrine->getRepository(\App\Entity\ForumThread::class);

    // Instantiate the radar service directly to avoid relying on a public service in the compiled container
    $radar = new \App\Service\ThreadDuplicateRadarService($repo);

    $currentUserId = $argv[1] ?? '3';
    $title = $argv[2] ?? 'How can I calm social anxiety before public speaking?';
    $content = $argv[3] ?? 'I get very anxious before talks — shaky voice, blanking out, and a racing heart. I usually have 30–60 minutes to prepare. What short routine (box breathing, visualization, grounding, brief exposure, mental rehearsal) reliably calms nerves and helps memory recall?';

    $results = $radar->findNearDuplicates($currentUserId, $title, $content, 10);

    echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
} catch (Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}

$kernel->shutdown();
