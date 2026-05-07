<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class DiseasePredictionService
{
    private const API_BASE_URL = 'http://127.0.0.1:5001';

    public function __construct(
        private readonly HttpClientInterface $httpClient
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function predictFromPrompt(string $prompt): array
    {
        $response = $this->httpClient->request('POST', self::API_BASE_URL . '/predict-from-prompt', [
            'json' => [
                'prompt' => $prompt,
            ],
        ]);

        return $response->toArray(false);
    }
}