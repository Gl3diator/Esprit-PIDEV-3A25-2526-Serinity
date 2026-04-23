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

// http_client may be private in compiled container; fall back to creating one
try {
    $httpClient = $container->get('http_client');
} catch (Throwable $e) {
    $httpClient = \Symfony\Component\HttpClient\HttpClient::create();
}

$apiKey = '';
try {
    $apiKey = $container->getParameter('app.translate_api_key');
} catch (Exception $e) {
    $apiKey = getenv('API_TRANSLATE_KEY') ?: '';
}

if ($apiKey === '') {
    echo "No API key configured (app.translate_api_key / API_TRANSLATE_KEY)\n";
}

$text = $argv[1] ?? "I am happy to help you. This is a test sentence about translation.";
$target = $argv[2] ?? 'French';

$endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey;
$prompt = sprintf("Translate the following text to %s. Only return the translated text:\n\n%s", $target, $text);

try {
    $response = $httpClient->request('POST', $endpoint, [
        'json' => [
            'contents' => [[
                'parts' => [[
                    'text' => $prompt,
                ]],
            ]],
        ],
        'timeout' => 60,
    ]);

    $status = $response->getStatusCode();
    $content = $response->getContent(false);
    echo "STATUS: $status\n";
    echo "RESPONSE: \n" . $content . "\n";

    $data = $response->toArray(false);
    $translated = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
    echo "TRANSLATED (extracted):\n" . ($translated ?? '[none]') . "\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

$kernel->shutdown();
