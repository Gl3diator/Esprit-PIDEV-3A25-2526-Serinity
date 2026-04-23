<?php

declare(strict_types=1);

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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

// create a tiny 1x1 PNG file in sys temp
$tmp = sys_get_temp_dir() . '/test_1x1.png';
$pngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNgYAAAAAMAASsJTYQAAAAASUVORK5CYII=';
file_put_contents($tmp, base64_decode($pngBase64));

$uploaded = new UploadedFile($tmp, 'test.png', 'image/png', null, true);

$httpClient = $container->get('http_client');
$imgbbKey = '';
try {
    $imgbbKey = $container->getParameter('app.imgbb_api_key');
} catch (Exception $e) {
    $imgbbKey = getenv('API_IMGBB') ?: '';
}

$uploadDir = $container->getParameter('app.upload_dir');

$service = new \App\Service\ImageUploadService($uploadDir, $httpClient, $imgbbKey);

try {
    $url = $service->upload($uploaded);
    echo "UPLOAD RESULT: " . ($url ?? 'NULL') . PHP_EOL;
} catch (Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}

$kernel->shutdown();

unlink($tmp);
