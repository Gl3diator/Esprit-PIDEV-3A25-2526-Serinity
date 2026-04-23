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

    $currentUserId = $argv[1] ?? '3';
    $title = $argv[2] ?? 'How can I calm social anxiety before public speaking?';
    $content = $argv[3] ?? 'I get very anxious before talks — shaky voice, blanking out, and a racing heart. I usually have 30–60 minutes to prepare. What short routine (box breathing, visualization, grounding, brief exposure, mental rehearsal) reliably calms nerves and helps memory recall?';

    $candidates = $repo->findDuplicateRadarCandidates(null, 60);
    if ($candidates === []) {
        echo json_encode([], JSON_PRETTY_PRINT) . PHP_EOL;
        $kernel->shutdown();
        exit(0);
    }

    $tokenize = function (string $text): array {
        $normalized = mb_strtolower($text);
        $normalized = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $normalized) ?? '';
        $parts = preg_split('/\s+/', $normalized, -1, PREG_SPLIT_NO_EMPTY);
        if (!is_array($parts)) {
            return [];
        }
        $tokens = [];
        foreach ($parts as $part) {
            if (mb_strlen($part) < 3) {
                continue;
            }
            $tokens[] = $part;
        }
        return $tokens;
    };

    $jaccard = function (array $a, array $b): float {
        if ($a === [] || $b === []) {
            return 0.0;
        }
        $setA = array_values(array_unique($a));
        $setB = array_values(array_unique($b));
        $intersection = count(array_intersect($setA, $setB));
        $union = count(array_unique(array_merge($setA, $setB)));
        if ($union === 0) {
            return 0.0;
        }
        return $intersection / $union;
    };

    $draftTitleTokens = $tokenize($title);
    $draftContentTokens = $tokenize($content);

    $lexical = [];
    foreach ($candidates as $thread) {
        $candidateTitleTokens = $tokenize((string) $thread->getTitle());
        $candidateContentTokens = $tokenize((string) $thread->getContent());

        $titleJaccard = $jaccard($draftTitleTokens, $candidateTitleTokens);
        $contentJaccard = $jaccard($draftContentTokens, $candidateContentTokens);
        similar_text(mb_strtolower(implode(' ', $draftTitleTokens)), mb_strtolower(implode(' ', $candidateTitleTokens)), $titlePercent);
        $titleFuzzy = $titlePercent / 100;

        $score = (0.5 * $titleJaccard) + (0.25 * $contentJaccard) + (0.25 * $titleFuzzy);
        if ($score < 0.24) {
            continue;
        }

        $lexical[] = ['thread' => $thread, 'lexical' => $score, 'score' => $score];
    }

    if ($lexical === []) {
        echo json_encode([], JSON_PRETTY_PRINT) . PHP_EOL;
        $kernel->shutdown();
        exit(0);
    }

    usort($lexical, static fn (array $a, array $b): int => $b['lexical'] <=> $a['lexical']);
    $shortlist = array_slice($lexical, 0, 10);
    usort($shortlist, static fn (array $a, array $b): int => $b['score'] <=> $a['score']);
    $threshold = 0.44;

    $duplicates = [];
    foreach ($shortlist as $item) {
        $score = (float) $item['score'];
        if ($score < $threshold) {
            continue;
        }

        $thread = $item['thread'];
        $createdAt = $thread->getCreatedAt();
        $ageDays = (int) max(0, $createdAt->diff(new \DateTimeImmutable())->days);

        $excerpt = function (string $text, int $maxLength): string {
            $text = trim(preg_replace('/\s+/', ' ', $text) ?? '');
            if (mb_strlen($text) <= $maxLength) {
                return $text;
            }
            return mb_substr($text, 0, $maxLength) . '...';
        };

        $duplicates[] = [
            'id' => (int) $thread->getId(),
            'title' => (string) $thread->getTitle(),
            'excerpt' => $excerpt((string) $thread->getContent(), 180),
            'category' => (string) ($thread->getCategory()?->getName() ?? 'Unknown'),
            'status' => $thread->getStatus()->value,
            'createdAt' => $createdAt->format('Y-m-d H:i'),
            'ageDays' => $ageDays,
            'score' => $score,
            'scorePercent' => (int) round($score * 100),
            'canRevive' => $thread->getStatus()->value === 'archived' || $ageDays >= 30,
        ];
    }

    echo json_encode($duplicates, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
} catch (Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}

$kernel->shutdown();
