<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class AmbientSoundService
{
    private const SEARCH_URL = 'https://de1.api.radio-browser.info/json/stations/search';

    /**
     * @var list<string>
     */
    private const SEARCH_TERMS = [
        'ambient',
        'piano',
        'meditation',
        'relaxation',
        'classical',
    ];

    /**
     * @var array<string,int>
     */
    private const PREFERRED_KEYWORDS = [
        'piano' => 5,
        'calm' => 4,
        'relaxing' => 4,
        'relaxation' => 4,
        'meditation' => 3,
        'sleep' => 3,
        'classical' => 2,
        'instrumental' => 2,
        'ambient' => 2,
        'soft' => 2,
        'peaceful' => 2,
    ];

    /**
     * @var list<string>
     */
    private const BLOCKED_KEYWORDS = [
        'rock',
        'metal',
        'dance',
        'techno',
        'electro',
        'edm',
        'rap',
        'hip hop',
        'pop',
        'news',
        'talk',
        'sports',
    ];

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @param array<string,mixed> $context
     * @return array{
     *     audioUrl:string,
     *     title:string,
     *     type:string,
     *     available:bool,
     *     tags:list<string>,
     *     tracks:list<array{
     *         title:string,
     *         type:string,
     *         tags:list<string>,
     *         url:string,
     *         source:string
     *     }>
     * }
     */
    public function getAmbientSound(array $context = []): array
    {
        if (!is_array($context)) {
            $context = [];
        }

        $soundType = $this->resolveSoundType($context);
        $selectedTrack = $this->findBestTrack($context);

        if ($selectedTrack === null) {
            $this->logger->warning('No valid calm ambient radio station found.', [
                'soundType' => $soundType,
            ]);

            return [
                'audioUrl' => '',
                'title' => '',
                'type' => $soundType,
                'available' => false,
                'tags' => [],
                'tracks' => [],
            ];
        }

        $this->logger->info('Ambient radio station selected.', [
            'title' => $selectedTrack['title'],
            'score' => $selectedTrack['score'],
        ]);

        return [
            'audioUrl' => $selectedTrack['url'],
            'title' => $selectedTrack['title'],
            'type' => $selectedTrack['type'],
            'available' => true,
            'tags' => $selectedTrack['tags'],
            'tracks' => [[
                'title' => $selectedTrack['title'],
                'type' => $selectedTrack['type'],
                'tags' => $selectedTrack['tags'],
                'url' => $selectedTrack['url'],
                'source' => $selectedTrack['source'],
            ]],
        ];
    }

    /**
     * @return array{
     *     title:string,
     *     type:string,
     *     tags:list<string>,
     *     url:string,
     *     source:string,
     *     score:int
     * }|null
     */
    private function findBestTrack(array $context): ?array
    {
        if (!is_array($context)) {
            $context = [];
        }

        $bestTrack = null;
        $bestScore = PHP_INT_MIN;
        $seenUrls = [];

        foreach ($this->resolveSearchTerms($context) as $term) {
            foreach ($this->searchStations($term) as $station) {
                $url = trim((string) ($station['url_resolved'] ?? ''));
                if ($url === '' || isset($seenUrls[$url])) {
                    continue;
                }

                $seenUrls[$url] = true;
                $track = [
                    'title' => $this->resolveTitle($station),
                    'type' => $this->resolveType($station),
                    'tags' => $this->resolveTags($station),
                    'url' => $url,
                    'source' => 'radio_browser',
                    'score' => $this->scoreStation($station),
                ];

                if ($track['score'] > $bestScore) {
                    $bestScore = $track['score'];
                    $bestTrack = $track;
                }
            }
        }

        return $bestScore > 0 ? $bestTrack : null;
    }

    /**
     * @return list<array<string,mixed>>
     */
    private function searchStations(string $term): array
    {
        try {
            $response = $this->httpClient->request('GET', self::SEARCH_URL, [
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'Serinity/1.0',
                ],
                'query' => [
                    'name' => $term,
                    'hidebroken' => 'true',
                    'limit' => 8,
                    'order' => 'clickcount',
                    'reverse' => 'true',
                ],
                'timeout' => 8.0,
            ]);

            if ($response->getStatusCode() !== 200) {
                $this->logger->warning('Radio Browser request returned a non-200 response.', [
                    'term' => $term,
                    'statusCode' => $response->getStatusCode(),
                ]);

                return [];
            }

            /** @var mixed $payload */
            $payload = $response->toArray(false);
            if (!is_array($payload)) {
                return [];
            }

            return array_values(array_filter($payload, static function (mixed $station): bool {
                if (!is_array($station)) {
                    return false;
                }

                $url = trim((string) ($station['url_resolved'] ?? ''));
                $lastCheckOk = (int) ($station['lastcheckok'] ?? 0);

                return $url !== '' && $lastCheckOk === 1;
            }));
        } catch (ExceptionInterface|\Throwable $exception) {
            $this->logger->warning('Radio Browser request failed.', [
                'term' => $term,
                'exception' => $exception,
            ]);

            return [];
        }
    }

    /**
     * @param array<string,mixed> $station
     */
    private function resolveTitle(array $station): string
    {
        $name = $this->cleanTitle(
            trim((string) ($station['name'] ?? '')),
            $this->resolveTags($station)
        );

        return $name !== '' ? $name : 'Ambient radio';
    }

    /**
     * @param array<string,mixed> $station
     */
    private function resolveType(array $station): string
    {
        $tags = $this->resolveTags($station);

        return $tags[0] ?? 'ambient';
    }

    /**
     * @param array<string,mixed> $station
     * @return list<string>
     */
    private function resolveTags(array $station): array
    {
        $rawTags = trim((string) ($station['tags'] ?? ''));
        if ($rawTags === '') {
            return ['ambient'];
        }

        $tags = array_values(array_filter(array_map(
            static fn(string $tag): string => trim(strtolower($tag)),
            explode(',', $rawTags)
        ), static fn(string $tag): bool => $tag !== ''));

        return $tags !== [] ? array_slice($tags, 0, 4) : ['ambient'];
    }

    /**
     * @param array<string,mixed> $station
     */
    private function scoreStation(array $station): int
    {
        $metadata = strtolower(trim(implode(' ', array_filter([
            (string) ($station['name'] ?? ''),
            (string) ($station['tags'] ?? ''),
        ]))));

        if ($metadata === '') {
            return PHP_INT_MIN;
        }

        $score = 0;

        foreach (self::PREFERRED_KEYWORDS as $keyword => $weight) {
            if (str_contains($metadata, $keyword)) {
                $score += $weight;
            }
        }

        foreach (self::BLOCKED_KEYWORDS as $keyword) {
            if (str_contains($metadata, $keyword)) {
                $score -= 10;
            }
        }

        return $score;
    }

    /**
     * @param list<string> $tags
     */
    private function cleanTitle(string $title, array $tags): string
    {
        $cleanTitle = trim($title);
        if ($cleanTitle === '') {
            return '';
        }

        if (str_starts_with($cleanTitle, 'OR - ')) {
            $cleanTitle = substr($cleanTitle, 5);
        }

        $cleanTitle = preg_split('/\|\|/u', $cleanTitle)[0] ?? $cleanTitle;
        $cleanTitle = preg_replace('/\s{2,}/', ' ', $cleanTitle) ?? $cleanTitle;
        $cleanTitle = trim($cleanTitle, " \t\n\r\0\x0B-:|");

        if (preg_match('/sleep|meditation|calm|relax|peaceful/i', $cleanTitle)) {
            return 'Calm Piano';
        }

        if (preg_match('/smooth jazz/i', $cleanTitle)) {
            return 'Smooth Jazz';
        }

        if (preg_match('/mozart|classical/i', $cleanTitle)) {
            return 'Classical Piano';
        }

        if (preg_match('/^[A-Z0-9 \-:]{18,}$/', $cleanTitle)) {
            if (in_array('piano', $tags, true)) {
                return 'Calm Piano';
            }

            if (in_array('classical', $tags, true)) {
                return 'Classical Piano';
            }

            if (in_array('ambient', $tags, true) || in_array('calm', $tags, true)) {
                return 'Calm Ambient';
            }
        }

        if (mb_strlen($cleanTitle) > 30) {
            $cleanTitle = rtrim(mb_substr($cleanTitle, 0, 30)) . '...';
        }

        return $cleanTitle;
    }

    /**
     * @param array<string,mixed> $context
     * @return list<string>
     */
    private function resolveSearchTerms(array $context): array
    {
        $soundType = $this->resolveSoundType($context);

        return match ($soundType) {
            'meditation' => ['meditation', 'ambient', 'relaxation', 'piano', 'classical'],
            'piano' => ['piano', 'classical', 'ambient', 'meditation', 'relaxation'],
            'ambient' => ['ambient', 'relaxation', 'meditation', 'piano', 'classical'],
            default => self::SEARCH_TERMS,
        };
    }

    /**
     * @param array<string,mixed> $context
     */
    private function resolveSoundType(array $context): string
    {
        $fatigue = strtolower(trim((string) ($context['fatigue'] ?? '')));
        $moment = strtolower(trim((string) ($context['moment'] ?? '')));
        $exerciseType = strtolower(trim((string) ($context['exerciseType'] ?? '')));

        return match (true) {
            $fatigue === 'high' => 'meditation',
            $moment === 'evening' || $moment === 'night' => 'piano',
            str_contains($exerciseType, 'respiration')
                || str_contains($exerciseType, 'breath')
                || str_contains($exerciseType, 'relax') => 'ambient',
            default => 'nature',
        };
    }
}
