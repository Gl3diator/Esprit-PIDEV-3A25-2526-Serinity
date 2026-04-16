<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class WeatherService
{
    private const API_URL = 'https://api.openweathermap.org/data/2.5/weather';

    private const CONDITION_MAP = [
        'clear' => 'ensoleillé',
        'rain' => 'pluvieux',
        'drizzle' => 'pluvieux',
        'thunderstorm' => 'pluvieux',
        'snow' => 'froid',
        'mist' => 'nuageux',
        'fog' => 'nuageux',
        'clouds' => 'nuageux',
    ];

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $weatherApiKey,
        private readonly string $city = 'Paris',
    ) {
    }

    public function getCurrentWeather(): string
    {
        $data = $this->fetchWeatherData();

        return $data['condition'];
    }

    public function getCurrentWeatherData(): array
    {
        return $this->fetchWeatherData();
    }

    private function fetchWeatherData(): array
    {
        try {
            $response = $this->httpClient->request('GET', self::API_URL, [
                'query' => [
                    'q' => $this->city,
                    'appid' => $this->weatherApiKey,
                    'units' => 'metric',
                    'lang' => 'en',
                ],
                'timeout' => 3,
            ]);

            $data = $response->toArray();

            if (!isset($data['weather'][0]['main'], $data['main']['temp'])) {
                return $this->fallback();
            }

            $condition = strtolower($data['weather'][0]['main']);
            $temperature = (float) $data['main']['temp'];

            $normalizedCondition = self::CONDITION_MAP[$condition] ?? 'nuageux';

            return [
                'condition' => $normalizedCondition,
                'temperature' => $temperature,
            ];
        } catch (\Exception $e) {
            return $this->fallback();
        }
    }

    private function fallback(): array
    {
        return [
            'condition' => 'nuageux',
            'temperature' => null,
        ];
    }
}