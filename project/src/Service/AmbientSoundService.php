<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class AmbientSoundService
{
    /**
     * @var list<string>
     */
    private const BASE_URLS = [
        'https://de1.api.radio-browser.info',
        'https://fr1.api.radio-browser.info',
        'https://at1.api.radio-browser.info',
        'https://all.api.radio-browser.info',
    ];

    private const FALLBACK_AUDIO_URL = 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3';
    private const MIN_ACCEPTABLE_SCORE = 24;

    /**
     * Positive words help the station score when they appear in Radio Browser metadata.
     *
     * @var list<string>
     */
    private const POSITIVE_KEYWORDS = [
        'calm',
        'meditation',
        'ambient',
        'zen',
        'relax',
        'sleep',
        'piano',
        'nature',
        'soft',
    ];

    /**
     * Strong negative matches are rejected completely so noisy stations never win.
     *
     * @var list<string>
     */
    private const HARD_BLOCKED_KEYWORDS = [
        'techno',
        'edm',
        'dubstep',
        'hardstyle',
        'trance',
        'club',
        'party',
    ];

    /**
     * @var list<string>
     */
    private const SOFT_BLOCKED_KEYWORDS = [
        'electro',
        'electronic',
        'house',
        'deep house',
        'dance',
        'dj',
        'beat',
        'remix',
        'metal',
        'rock',
    ];

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @param array<string,mixed> $context
     * @return array{audioUrl:string,title:string,type:string,query?:string,source?:string}
     */
    public function getAmbientSound(array $context = []): array
    {
        $normalizedContext = $this->normalizeContext($context);
        $queries = $this->buildSearchQueries($normalizedContext);
        $bestCandidate = null;
        $bestScore = PHP_INT_MIN;

        $this->logger->info('Ambient sound recommendation started.', [
            'context' => $normalizedContext,
            'queries' => $queries,
        ]);

        foreach ($queries as $query) {
            foreach (self::BASE_URLS as $baseUrl) {
                $candidate = $this->searchStations($baseUrl, $query, $normalizedContext);
                if ($candidate === null) {
                    continue;
                }

                if (($candidate['score'] ?? PHP_INT_MIN) > $bestScore) {
                    $bestCandidate = $candidate;
                    $bestScore = (int) $candidate['score'];
                }
            }
        }

        if ($bestCandidate !== null && $bestScore >= self::MIN_ACCEPTABLE_SCORE) {
            unset($bestCandidate['score']);

            return $bestCandidate;
        }

        $this->logger->warning('No suitable ambient station found. Using fallback audio.', [
            'context' => $normalizedContext,
            'queries' => $queries,
            'bestScore' => $bestScore,
            'fallbackUrl' => self::FALLBACK_AUDIO_URL,
        ]);

        return $this->buildFallbackResult($normalizedContext);
    }

    /**
     * @param array<string,string> $context
     * @return array{audioUrl:string,title:string,type:string,query:string,source:string,score:int}|null
     */
    private function searchStations(string $baseUrl, string $query, array $context): ?array
    {
        try {
            $response = $this->httpClient->request('GET', rtrim($baseUrl, '/') . '/json/stations/search', [
                'headers' => [
                    'User-Agent' => 'Serinity/1.0',
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'name' => $query,
                    'hidebroken' => 'true',
                    'limit' => 40,
                    'order' => 'clicktrend',
                    'reverse' => 'true',
                ],
                'timeout' => 8.0,
            ]);

            if ($response->getStatusCode() !== 200) {
                $this->logger->warning('Radio Browser returned a non-200 response.', [
                    'baseUrl' => $baseUrl,
                    'query' => $query,
                    'statusCode' => $response->getStatusCode(),
                ]);

                return null;
            }

            /** @var mixed $payload */
            $payload = $response->toArray(false);
            if (!is_array($payload)) {
                return null;
            }

            return $this->selectBestStation($payload, $context, $baseUrl, $query);
        } catch (ExceptionInterface|\Throwable $exception) {
            $this->logger->warning('Ambient sound lookup failed for a Radio Browser mirror.', [
                'baseUrl' => $baseUrl,
                'query' => $query,
                'exception' => $exception,
            ]);

            return null;
        }
    }

    /**
     * @param list<array<string,mixed>> $stations
     * @param array<string,string> $context
     * @return array{audioUrl:string,title:string,type:string,query:string,source:string,score:int}|null
     */
    private function selectBestStation(array $stations, array $context, string $baseUrl, string $query): ?array
    {
        $bestStation = null;
        $bestScore = PHP_INT_MIN;
        $queryTerms = $this->splitTerms($query);
        $soundType = $context['soundType'];

        foreach ($stations as $station) {
            if (!is_array($station)) {
                continue;
            }

            $audioUrl = $this->resolveStationUrl($station);
            if ($audioUrl === null) {
                continue;
            }

            $metadata = $this->buildMetadata($station);
            if ($metadata === '' || $this->containsAny($metadata, self::HARD_BLOCKED_KEYWORDS)) {
                continue;
            }

            $score = $this->scoreStation($station, $metadata, $queryTerms, $context);
            if ($score <= $bestScore) {
                continue;
            }

            $bestScore = $score;
            $bestStation = [
                'audioUrl' => $audioUrl,
                'title' => $this->resolveStationTitle($station, $context),
                'type' => $soundType,
                'query' => $query,
                'source' => $baseUrl,
                'score' => $score,
            ];
        }

        if ($bestStation !== null) {
            $this->logger->info('Ambient station candidate selected from mirror.', [
                'query' => $query,
                'source' => $baseUrl,
                'title' => $bestStation['title'],
                'score' => $bestScore,
                'type' => $soundType,
            ]);
        }

        return $bestStation;
    }

    /**
     * @param array<string,mixed> $station
     * @param list<string> $queryTerms
     * @param array<string,string> $context
     */
    private function scoreStation(array $station, string $metadata, array $queryTerms, array $context): int
    {
        $score = 0;

        foreach ($queryTerms as $term) {
            if (str_contains($metadata, $term)) {
                $score += 12;
            }
        }

        foreach (self::POSITIVE_KEYWORDS as $keyword) {
            if (str_contains($metadata, $keyword)) {
                $score += 6;
            }
        }

        foreach (self::SOFT_BLOCKED_KEYWORDS as $keyword) {
            if (str_contains($metadata, $keyword)) {
                $score -= 16;
            }
        }

        if ($context['fatigue'] === 'high') {
            $score += $this->countMatches($metadata, ['calm', 'sleep', 'meditation', 'soft']) * 5;
        }

        if (in_array($context['moment'], ['evening', 'night'], true)) {
            $score += $this->countMatches($metadata, ['piano', 'soft', 'ambient', 'sleep']) * 4;
        }

        if ($context['exerciseIntent'] === 'breathing') {
            $score += $this->countMatches($metadata, ['ambient', 'zen', 'meditation', 'calm']) * 5;
        }

        if ($context['exerciseIntent'] === 'focus') {
            $score += $this->countMatches($metadata, ['nature', 'ambient', 'soft']) * 5;
        }

        if ($context['exerciseIntent'] === 'meditation') {
            $score += $this->countMatches($metadata, ['meditation', 'zen', 'calm', 'ambient']) * 5;
        }

        if ($context['weather'] === 'rain') {
            $score += $this->countMatches($metadata, ['calm', 'piano', 'relax', 'nature']) * 4;
        }

        if ($context['theme'] !== '' && str_contains($metadata, $context['theme'])) {
            $score += 8;
        }

        $votes = max(0, (int) ($station['votes'] ?? 0));
        $clickCount = max(0, (int) ($station['clickcount'] ?? 0));
        $bitrate = max(0, (int) ($station['bitrate'] ?? 0));

        $score += min(10, (int) floor($votes / 20));
        $score += min(8, (int) floor($clickCount / 40));

        // Prefer stable, lighter streams for long calming sessions without over-valuing bitrate.
        if ($bitrate >= 32 && $bitrate <= 192) {
            $score += 4;
        } elseif ($bitrate > 0 && $bitrate < 32) {
            $score -= 4;
        }

        return $score;
    }

    /**
     * @param array<string,mixed> $station
     */
    private function buildMetadata(array $station): string
    {
        return strtolower(trim(implode(' ', array_filter([
            (string) ($station['name'] ?? ''),
            (string) ($station['tags'] ?? ''),
            (string) ($station['homepage'] ?? ''),
            (string) ($station['country'] ?? ''),
            (string) ($station['state'] ?? ''),
            (string) ($station['language'] ?? ''),
            (string) ($station['codec'] ?? ''),
        ]))));
    }

    /**
     * @param array<string,mixed> $station
     */
    private function resolveStationUrl(array $station): ?string
    {
        foreach (['url_resolved', 'url'] as $field) {
            $value = trim((string) ($station[$field] ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param array<string,mixed> $station
     * @param array<string,string> $context
     */
    private function resolveStationTitle(array $station, array $context): string
    {
        $title = trim((string) ($station['name'] ?? ''));

        return $title !== '' ? $title : $this->resolveFallbackTitle($context['soundType']);
    }

    /**
     * The recommendation stays context-driven: we normalize user state into a small set
     * of predictable values before generating queries or scoring live stations.
     *
     * @param array<string,mixed> $context
     * @return array<string,string>
     */
    private function normalizeContext(array $context): array
    {
        $weather = $this->normalizeWeather((string) ($context['weather'] ?? $context['weatherLabel'] ?? ''));
        $moment = $this->normalizeMoment((string) ($context['moment'] ?? ''));
        $fatigue = $this->normalizeFatigue((string) ($context['fatigue'] ?? ''));
        $exerciseType = strtolower(trim((string) ($context['exerciseType'] ?? $context['recommendationType'] ?? '')));
        $theme = strtolower(trim((string) ($context['exerciseTheme'] ?? $context['theme'] ?? '')));
        $exerciseIntent = $this->resolveExerciseIntent($exerciseType);

        return [
            'weather' => $weather,
            'moment' => $moment,
            'fatigue' => $fatigue,
            'exerciseType' => $exerciseType,
            'exerciseIntent' => $exerciseIntent,
            'theme' => $theme,
            'soundType' => $this->resolveSoundType($exerciseIntent, $moment, $fatigue, $weather),
        ];
    }

    private function normalizeWeather(string $weather): string
    {
        $weather = strtolower(trim($weather));

        return match (true) {
            $weather === '' => 'neutral',
            str_contains($weather, 'rain') || str_contains($weather, 'drizzle') || str_contains($weather, 'storm') => 'rain',
            str_contains($weather, 'snow') => 'snow',
            str_contains($weather, 'sun') || str_contains($weather, 'clear') => 'clear',
            default => 'neutral',
        };
    }

    private function normalizeMoment(string $moment): string
    {
        $moment = strtolower(trim($moment));

        return in_array($moment, ['morning', 'afternoon', 'evening', 'night'], true) ? $moment : 'day';
    }

    private function normalizeFatigue(string $fatigue): string
    {
        $fatigue = strtolower(trim($fatigue));

        return in_array($fatigue, ['low', 'medium', 'high'], true) ? $fatigue : 'medium';
    }

    private function resolveExerciseIntent(string $exerciseType): string
    {
        return match (true) {
            str_contains($exerciseType, 'breath') || str_contains($exerciseType, 'respiration') => 'breathing',
            str_contains($exerciseType, 'focus') || str_contains($exerciseType, 'concentration') => 'focus',
            str_contains($exerciseType, 'meditation') || str_contains($exerciseType, 'relax') || str_contains($exerciseType, 'mind') => 'meditation',
            default => 'general',
        };
    }

    private function resolveSoundType(string $exerciseIntent, string $moment, string $fatigue, string $weather): string
    {
        return match (true) {
            $fatigue === 'high' => 'calm',
            $exerciseIntent === 'meditation' || $exerciseIntent === 'breathing' => 'meditation',
            $exerciseIntent === 'focus' => 'nature',
            $moment === 'evening' || $moment === 'night' => 'piano',
            $weather === 'rain' => 'ambient',
            default => 'balanced',
        };
    }

    /**
     * Queries are generated from context instead of hard-coded sound type strings.
     * This keeps the recommendation adaptable and easy to reason about.
     *
     * @param array<string,string> $context
     * @return list<string>
     */
    private function buildSearchQueries(array $context): array
    {
        $queries = [];

        foreach ($this->queriesForExerciseIntent($context['exerciseIntent']) as $query) {
            $queries[] = $query;
        }

        foreach ($this->queriesForFatigue($context['fatigue']) as $query) {
            $queries[] = $query;
        }

        foreach ($this->queriesForMoment($context['moment']) as $query) {
            $queries[] = $query;
        }

        foreach ($this->queriesForWeather($context['weather']) as $query) {
            $queries[] = $query;
        }

        if ($context['theme'] !== '') {
            $queries[] = $context['theme'] . ' ambient';
            $queries[] = $context['theme'] . ' calm';
        }

        $queries[] = match ($context['soundType']) {
            'calm' => 'calm meditation',
            'meditation' => 'zen meditation',
            'nature' => 'nature sounds',
            'piano' => 'soft piano',
            'ambient' => 'calm ambient',
            default => 'balanced ambient',
        };
        $queries[] = 'soft piano';
        $queries[] = 'calm ambient';
        $queries[] = 'nature sounds';
        $queries[] = 'zen meditation';

        return array_values(array_unique(array_filter(array_map(
            static fn(string $query): string => trim(strtolower($query)),
            $queries
        ))));
    }

    /**
     * @return list<string>
     */
    private function queriesForExerciseIntent(string $exerciseIntent): array
    {
        return match ($exerciseIntent) {
            'breathing' => ['ambient zen', 'calm breathing meditation', 'zen meditation'],
            'focus' => ['nature sounds', 'soft ambient', 'calm nature'],
            'meditation' => ['piano meditation', 'calm ambient', 'zen meditation'],
            default => ['calm ambient', 'soft piano', 'nature relax'],
        };
    }

    /**
     * @return list<string>
     */
    private function queriesForFatigue(string $fatigue): array
    {
        return match ($fatigue) {
            'high' => ['sleep relax', 'soft piano', 'calm meditation'],
            'low' => ['balanced ambient', 'nature sounds'],
            default => ['calm ambient', 'relax piano'],
        };
    }

    /**
     * @return list<string>
     */
    private function queriesForMoment(string $moment): array
    {
        return match ($moment) {
            'evening', 'night' => ['soft piano', 'sleep relax', 'calm ambient'],
            'morning' => ['nature sounds', 'soft ambient'],
            default => ['piano meditation', 'nature relax'],
        };
    }

    /**
     * @return list<string>
     */
    private function queriesForWeather(string $weather): array
    {
        return match ($weather) {
            'rain' => ['warm calm sounds', 'calm ambient', 'soft piano'],
            'snow' => ['soft piano', 'sleep relax'],
            'clear' => ['nature sounds', 'zen meditation'],
            default => ['calm ambient'],
        };
    }

    /**
     * @param array<string,string> $context
     * @return array{audioUrl:string,title:string,type:string,query:string,source:string}
     */
    private function buildFallbackResult(array $context): array
    {
        return [
            'audioUrl' => self::FALLBACK_AUDIO_URL,
            'title' => $this->resolveFallbackTitle($context['soundType']),
            'type' => $context['soundType'],
            'query' => 'fallback',
            'source' => 'soundhelix',
        ];
    }

    /**
     * @return list<string>
     */
    private function splitTerms(string $query): array
    {
        return array_values(array_filter(
            preg_split('/\s+/', strtolower(trim($query))) ?: [],
            static fn(string $term): bool => $term !== ''
        ));
    }

    /**
     * @param list<string> $needles
     */
    private function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param list<string> $needles
     */
    private function countMatches(string $haystack, array $needles): int
    {
        $matches = 0;
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                ++$matches;
            }
        }

        return $matches;
    }

    private function resolveFallbackTitle(string $soundType): string
    {
        return match ($soundType) {
            'calm' => 'Calm recovery fallback',
            'meditation' => 'Meditation fallback',
            'nature' => 'Nature focus fallback',
            'piano' => 'Soft piano fallback',
            'ambient' => 'Ambient relaxation fallback',
            default => 'Balanced ambient fallback',
        };
    }
}
