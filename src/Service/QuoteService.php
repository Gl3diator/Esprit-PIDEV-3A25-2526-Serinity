<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class QuoteService
{
    private const FALLBACK_QUOTE = [
        'text' => 'Prenez un instant pour respirer et revenir à vous.',
        'author' => 'Serinity',
    ];

    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    public function getRandomQuote(): array
    {
        try {
            $response = $this->httpClient->request('GET', 'https://zenquotes.io/api/random', [
                'timeout' => 3,
            ]);

            $data = $response->toArray();

            if (!empty($data) && isset($data[0]['q']) && isset($data[0]['a'])) {
                return [
                    'text' => $data[0]['q'],
                    'author' => $data[0]['a'],
                ];
            }
        } catch (\Exception $e) {
        }

        return self::FALLBACK_QUOTE;
    }
}