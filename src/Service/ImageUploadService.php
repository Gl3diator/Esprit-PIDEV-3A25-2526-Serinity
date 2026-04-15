<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ImageUploadService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $imageRequestUrl,
        private readonly string $imageApiKey,
    ) {
    }

    public function uploadProfileImage(UploadedFile $file): string
    {
        $binary = file_get_contents($file->getPathname());
        if ($binary === false) {
            throw new \RuntimeException('Unable to read uploaded image.');
        }

        $response = $this->httpClient->request('POST', $this->imageRequestUrl, [
            'body' => [
                'key' => $this->imageApiKey,
                'source' => base64_encode($binary),
                'format' => 'json',
            ],
        ]);

        $payload = $response->toArray(false);
        $imageUrl = $payload['image']['url']
            ?? $payload['data']['display_url']
            ?? $payload['data']['url']
            ?? $payload['url']
            ?? null;

        if (!is_string($imageUrl) || $imageUrl === '') {
            throw new \RuntimeException('Image upload failed.');
        }

        return $imageUrl;
    }
}
