<?php

declare(strict_types=1);

namespace App\Service\User;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class ExerciseProfileApiClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private string $baseUrl,
        private float $timeout,
    ) {
    }

    /**
     * @param array<string, float|int> $features
     * @return array{profile:string,probabilities?:array<string,float>}|null
     */
    public function predict(array $features): ?array
    {
        $url = rtrim($this->baseUrl, '/') . '/predict';

        try {
            $response = $this->httpClient->request('POST', $url, [
                'json' => ['features' => $features],
                'timeout' => $this->timeout,
            ]);

            if ($response->getStatusCode() !== 200) {
                $this->logger->warning('Exercise profile API returned a non-200 response.', [
                    'status_code' => $response->getStatusCode(),
                ]);

                return null;
            }

            $payload = $response->toArray(false);
            if (!is_array($payload)) {
                return null;
            }

            $profile = is_string($payload['profile'] ?? null) ? trim((string) $payload['profile']) : '';
            if ($profile === '') {
                return null;
            }

            $probabilities = [];
            foreach (($payload['probabilities'] ?? []) as $label => $score) {
                if (is_string($label) && is_numeric($score)) {
                    $probabilities[$label] = round((float) $score, 4);
                }
            }

            return [
                'profile' => $profile,
                'probabilities' => $probabilities,
            ];
        } catch (ExceptionInterface|\Throwable $exception) {
            $this->logger->warning('Exercise profile API request failed.', [
                'exception' => $exception,
            ]);

            return null;
        }
    }
}
