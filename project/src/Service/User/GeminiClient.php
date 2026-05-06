<?php

declare(strict_types=1);

namespace App\Service\User;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class GeminiClient
{
    private const BASE_URL = 'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent';
    private const REQUEST_TIMEOUT_SECONDS = 30.0;
    private const REQUEST_MAX_DURATION_SECONDS = 30.0;
    private const MAX_ATTEMPTS = 3;
    private const RETRY_DELAY_MICROSECONDS = 1000000;

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private string $apiKey,
        private string $model,
    ) {
    }

    /**
     * @param array<string,mixed> $report
     * @return array{
     *     summary:string,
     *     strengths:list<string>,
     *     improvements:list<string>,
     *     recommendations:list<string>,
     *     plan7Days:list<string>,
     *     nutritionSupport:array{
     *         focus:string,
     *         foods:list<string>,
     *         dishes:list<string>,
     *         note:string
     *     },
     *     tone:string,
     *     source:string
     * }|null
     */
    public function generateCoachInsight(array $report): ?array
    {
        $this->logger->info('Gemini coach client invoked.', [
            'model' => $this->model,
            'has_api_key' => trim($this->apiKey) !== '',
        ]);

        if (!$this->hasUsableConfiguration()) {
            $this->logger->warning('Gemini coach client skipped because configuration is incomplete.', [
                'has_api_key' => trim($this->apiKey) !== '',
                'has_model' => trim($this->model) !== '',
            ]);

            return null;
        }

        $candidateText = $this->requestCandidateText($this->buildPrompt($report), 'coach');
        if ($candidateText === null) {
            return null;
        }

        try {
            $coachPayload = json_decode($candidateText, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            $this->logger->warning('Gemini coach candidate text contained invalid JSON.', [
                'exception_class' => $exception::class,
                'exception_message' => $exception->getMessage(),
                'candidate_text' => $candidateText,
                'fallback_reason' => 'invalid_candidate_json',
            ]);

            return null;
        }

        $insight = $this->normalizeInsight($coachPayload);
        if ($insight === null) {
            $this->logger->warning('Gemini coach response JSON did not match the expected insight structure.', [
                'candidate_text' => $candidateText,
                'fallback_reason' => 'unexpected_insight_structure',
            ]);

            return null;
        }

        return $insight;
    }

    public function generateCoachChatReply(string $prompt): ?string
    {
        $this->logger->info('Gemini coach chat client invoked.', [
            'model' => $this->model,
            'has_api_key' => trim($this->apiKey) !== '',
            'prompt_length' => mb_strlen($prompt),
        ]);

        if (!$this->hasUsableConfiguration() || trim($prompt) === '') {
            $this->logger->warning('Gemini coach chat client skipped because configuration or prompt is incomplete.', [
                'has_api_key' => trim($this->apiKey) !== '',
                'has_model' => trim($this->model) !== '',
                'has_prompt' => trim($prompt) !== '',
            ]);

            return null;
        }

        return $this->requestCandidateText($prompt, 'coach chat');
    }

    /** @param array<string,mixed> $report */
    private function buildPrompt(array $report): string
    {
        $reportJson = json_encode($report, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
You are a calm, supportive wellness coach inside a mindfulness and exercise application called "Serinity".

Your role is NOT to generate random advice.
You must interpret real user activity data and provide meaningful, realistic, and personalized coaching.

You are helping a beginner or low-consistency user build a gentle and sustainable routine.

----------------------------------------
USER PERFORMANCE DATA
----------------------------------------
{$reportJson}

----------------------------------------
COACHING OBJECTIVES
----------------------------------------

1. Write a short, human, and motivating summary (2-3 sentences)
- Acknowledge progress, even if small
- Be encouraging but NOT exaggerated
- Avoid generic phrases like "great job" without context

2. Identify 2 strengths
- Based on real patterns (time, type, consistency)
- Be specific and grounded

3. Identify 2 improvement areas
- Frame them gently
- Focus on realistic progression
- Avoid negative or judgmental tone

4. Provide 3 personalized recommendations
Each recommendation must:
- be actionable
- be specific (time, type, or context)
- reflect the user's habits (evening, breathing, low activity, etc.)
- avoid vague phrases like "explore more"

5. Generate a realistic 7-day plan

VERY IMPORTANT RULES:
- Each day must include a SMALL but REAL action
- No "just open the app"
- No "no action needed"
- Keep actions simple but meaningful
- Vary durations (1-3 minutes max)
- Vary activity types (breathing, stretch, pause, body awareness)
- Adapt to a beginner level
- Build gentle progression (not intensity)

Examples of good actions:
- "Complete a 2-minute breathing reset in the evening"
- "Add a 1-minute body awareness pause in the afternoon"
- "Repeat your favorite exercise once today"
- "Try a short stretch after sitting for a long period"

Avoid:
- repetition of identical durations every day
- overly passive suggestions
- unrealistic plans

6. Add a brief "Nutrition support" section
This is light wellness support, not medical guidance.
It must:
- focus on food-first suggestions
- fit the user's activity pattern, such as low energy, inconsistent activity, evening stress, short sessions, recovery needs, or calm/sleep-oriented routines
- suggest simple foods and practical dish ideas
- avoid diagnosis, deficiency claims, supplement prescriptions, doses, or medical treatment language
- avoid saying the user "needs" a vitamin, mineral, supplement, or treatment
- use gentle conditional wording when mentioning nutrients

Suitable themes may include:
- iron-rich foods
- B12-rich foods
- folate-rich foods
- magnesium-rich foods
- vitamin D discussion only as a soft suggestion if fatigue remains persistent

Suitable foods include:
- eggs
- lentils
- spinach
- chickpeas
- yogurt
- nuts
- salmon
- oats
- bananas
- leafy greens

Suitable dishes include:
- lentil and spinach soup
- yogurt with oats and banana
- egg and avocado toast
- salmon with vegetables
- chickpea salad

Suitable note examples:
- "If tiredness stays high over time, iron, B12, folate, or vitamin D may be worth discussing with a healthcare professional."
- "Focus on regular meals and energy-supporting foods before considering supplements."
- "A balanced routine with nourishing meals may support energy and recovery."

----------------------------------------
OUTPUT FORMAT (STRICT JSON ONLY)
----------------------------------------

Return ONLY valid JSON with this exact structure:

{
  "summary": "string",
  "strengths": ["string", "string"],
  "improvements": ["string", "string"],
  "recommendations": ["string", "string", "string"],
  "plan7Days": ["string", "string", "string", "string", "string", "string", "string"],
  "nutritionSupport": {
    "focus": "string",
    "foods": ["string", "string", "string"],
    "dishes": ["string", "string"],
    "note": "string"
  },
  "tone": "supportive"
}

Do not add explanations, markdown, or extra text.
Only return valid JSON.
PROMPT;
    }

    /** @param array<string,mixed> $payload */
    private function extractCandidateText(array $payload): string
    {
        $text = '';
        $parts = $payload['candidates'][0]['content']['parts'] ?? [];
        if (is_array($parts)) {
            foreach ($parts as $part) {
                if (is_array($part) && is_string($part['text'] ?? null)) {
                    $text .= $part['text'];
                }
            }
        }

        $text = trim($text);
        if ($text === '') {
            return '';
        }

        return trim(preg_replace('/^```(?:json)?\s*|\s*```$/', '', $text) ?? $text);
    }

    /**
     * @param array<string,mixed> $payload
     * @return array{
     *     candidate_count:int,
     *     first_candidate_has_content:bool,
     *     first_candidate_part_count:int,
     *     text_part_count:int,
     *     blank_text_part_count:int
     * }
     */
    private function candidatePayloadDiagnostics(array $payload): array
    {
        $candidates = is_array($payload['candidates'] ?? null) ? $payload['candidates'] : [];
        $firstCandidate = is_array($candidates[0] ?? null) ? $candidates[0] : [];
        $content = is_array($firstCandidate['content'] ?? null) ? $firstCandidate['content'] : [];
        $parts = is_array($content['parts'] ?? null) ? $content['parts'] : [];
        $textPartCount = 0;
        $blankTextPartCount = 0;

        foreach ($parts as $part) {
            if (!is_array($part) || !is_string($part['text'] ?? null)) {
                continue;
            }

            ++$textPartCount;
            if (trim($part['text']) === '') {
                ++$blankTextPartCount;
            }
        }

        return [
            'candidate_count' => count($candidates),
            'first_candidate_has_content' => $content !== [],
            'first_candidate_part_count' => count($parts),
            'text_part_count' => $textPartCount,
            'blank_text_part_count' => $blankTextPartCount,
        ];
    }

    /**
     * @return array{
     *     summary:string,
     *     strengths:list<string>,
     *     improvements:list<string>,
     *     recommendations:list<string>,
     *     plan7Days:list<string>,
     *     nutritionSupport:array{
     *         focus:string,
     *         foods:list<string>,
     *         dishes:list<string>,
     *         note:string
     *     },
     *     tone:string,
     *     source:string
     * }|null
     */
    private function normalizeInsight(mixed $payload): ?array
    {
        if (!is_array($payload)) {
            return null;
        }

        $summary = $this->stringValue($payload['summary'] ?? null);
        $strengths = $this->stringList($payload['strengths'] ?? null, 2, 4);
        $improvements = $this->stringList($payload['improvements'] ?? null, 2, 4);
        $recommendations = $this->stringList($payload['recommendations'] ?? null, 3, 5);
        $plan7Days = $this->stringList($payload['plan7Days'] ?? null, 7, 7);
        $nutritionSupport = $this->nutritionSupport($payload['nutritionSupport'] ?? null);

        if ($summary === '' || count($strengths) < 2 || count($improvements) < 2 || count($recommendations) < 3 || count($plan7Days) !== 7 || $nutritionSupport === null) {
            return null;
        }

        return [
            'summary' => $summary,
            'strengths' => $strengths,
            'improvements' => $improvements,
            'recommendations' => $recommendations,
            'plan7Days' => $plan7Days,
            'nutritionSupport' => $nutritionSupport,
            'tone' => 'supportive',
            'source' => 'ai',
        ];
    }

    private function stringValue(mixed $value): string
    {
        return is_string($value) ? trim($value) : '';
    }

    private function hasUsableConfiguration(): bool
    {
        return trim($this->apiKey) !== '' && trim($this->model) !== '';
    }

    private function requestCandidateText(string $prompt, string $context): ?string
    {
        $requestOptions = [
            'query' => ['key' => $this->apiKey],
            'json' => [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
            ],
            'timeout' => self::REQUEST_TIMEOUT_SECONDS,
            'max_duration' => self::REQUEST_MAX_DURATION_SECONDS,
        ];

        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; ++$attempt) {
            try {
                $this->logger->info(sprintf('Gemini %s HTTP request is about to be sent.', $context), [
                    'model' => $this->model,
                    'attempt' => $attempt,
                    'max_attempts' => self::MAX_ATTEMPTS,
                    'timeout' => self::REQUEST_TIMEOUT_SECONDS,
                    'max_duration' => self::REQUEST_MAX_DURATION_SECONDS,
                ]);

                $response = $this->httpClient->request('POST', sprintf(self::BASE_URL, rawurlencode($this->model)), $requestOptions);
                $statusCode = $response->getStatusCode();
                $rawBody = $response->getContent(false);

                $shouldRetry = $this->shouldRetryStatusCode($statusCode, $attempt);
                $this->logger->info(sprintf('Gemini %s HTTP response received.', $context), [
                    'model' => $this->model,
                    'attempt' => $attempt,
                    'max_attempts' => self::MAX_ATTEMPTS,
                    'status_code' => $statusCode,
                    'retry_triggered' => $shouldRetry,
                    'raw_body' => $rawBody,
                ]);

                if ($statusCode !== 200) {
                    $this->logger->warning($this->httpFailureMessage($context, $statusCode, $rawBody), [
                        'attempt' => $attempt,
                        'max_attempts' => self::MAX_ATTEMPTS,
                        'status_code' => $statusCode,
                        'retry_triggered' => $shouldRetry,
                        'retry_blocked' => in_array($statusCode, [403, 429], true),
                        'raw_body' => $rawBody,
                        'fallback_reason' => sprintf('http_%d', $statusCode),
                    ]);

                    if ($shouldRetry) {
                        $this->logRetryScheduled($context, $attempt, sprintf('http_%d', $statusCode), $statusCode);
                        usleep(self::RETRY_DELAY_MICROSECONDS);

                        continue;
                    }

                    return null;
                }

                if (trim($rawBody) === '') {
                    $this->logger->warning(sprintf('Gemini %s response body was empty.', $context), [
                        'attempt' => $attempt,
                        'max_attempts' => self::MAX_ATTEMPTS,
                        'status_code' => $statusCode,
                        'retry_triggered' => false,
                        'fallback_reason' => 'empty_response_body',
                    ]);

                    return null;
                }

                try {
                    $payload = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $exception) {
                    $this->logger->warning(sprintf('Gemini %s HTTP response contained invalid JSON.', $context), [
                        'exception_class' => $exception::class,
                        'exception_message' => $exception->getMessage(),
                        'attempt' => $attempt,
                        'max_attempts' => self::MAX_ATTEMPTS,
                        'status_code' => $statusCode,
                        'retry_triggered' => false,
                        'raw_body' => $rawBody,
                        'fallback_reason' => 'invalid_http_json',
                    ]);

                    return null;
                }

                if (!is_array($payload)) {
                    $this->logger->warning(sprintf('Gemini %s HTTP response JSON was not an object.', $context), [
                        'attempt' => $attempt,
                        'max_attempts' => self::MAX_ATTEMPTS,
                        'status_code' => $statusCode,
                        'retry_triggered' => false,
                        'raw_body' => $rawBody,
                        'fallback_reason' => 'unexpected_http_json_shape',
                    ]);

                    return null;
                }

                $candidateDiagnostics = $this->candidatePayloadDiagnostics($payload);
                $candidateText = $this->extractCandidateText($payload);
                if ($candidateText === '') {
                    $this->logger->warning(sprintf('Gemini %s response did not contain candidate text.', $context), [
                        'attempt' => $attempt,
                        'max_attempts' => self::MAX_ATTEMPTS,
                        'status_code' => $statusCode,
                        'retry_triggered' => false,
                        'candidate_diagnostics' => $candidateDiagnostics,
                        'raw_body' => $rawBody,
                        'fallback_reason' => 'empty_candidate_text',
                    ]);

                    return null;
                }

                if ($attempt > 1) {
                    $this->logger->info(sprintf('Gemini %s retry succeeded.', $context), [
                        'model' => $this->model,
                        'attempt' => $attempt,
                        'max_attempts' => self::MAX_ATTEMPTS,
                    ]);
                }

                return $candidateText;
            } catch (TransportExceptionInterface $exception) {
                $message = $exception->getMessage();
                $isTimeout = $this->isTimeoutException($message);
                $shouldRetry = $isTimeout && $attempt < self::MAX_ATTEMPTS;
                $fallbackReason = $isTimeout ? 'transport_timeout' : 'transport_error';

                $this->logger->warning(
                    sprintf(
                        'Gemini %s %s.',
                        $context,
                        $isTimeout ? 'request timed out' : 'transport failed'
                    ),
                    [
                        'exception_class' => $exception::class,
                        'exception_message' => $message,
                        'model' => $this->model,
                        'attempt' => $attempt,
                        'max_attempts' => self::MAX_ATTEMPTS,
                        'retry_triggered' => $shouldRetry,
                        'curl_loaded' => extension_loaded('curl'),
                        'likely_fopen_transport' => str_contains(strtolower($message), 'fopen') || !extension_loaded('curl'),
                        'fallback_reason' => $fallbackReason,
                    ]
                );

                if ($shouldRetry) {
                    $this->logRetryScheduled($context, $attempt, $fallbackReason, null);
                    usleep(self::RETRY_DELAY_MICROSECONDS);

                    continue;
                }

                return null;
            } catch (\Throwable $exception) {
                $this->logger->warning(sprintf('Gemini %s request failed unexpectedly.', $context), [
                    'exception_class' => $exception::class,
                    'exception_message' => $exception->getMessage(),
                    'model' => $this->model,
                    'attempt' => $attempt,
                    'max_attempts' => self::MAX_ATTEMPTS,
                    'retry_triggered' => false,
                    'fallback_reason' => 'unexpected_exception',
                ]);

                return null;
            }
        }

        $this->logger->warning(sprintf('Gemini %s exhausted all attempts and will return null for fallback handling.', $context), [
            'model' => $this->model,
            'max_attempts' => self::MAX_ATTEMPTS,
            'fallback_reason' => 'attempts_exhausted',
        ]);

        return null;
    }

    private function shouldRetryStatusCode(int $statusCode, int $attempt): bool
    {
        return $statusCode === 503 && $attempt < self::MAX_ATTEMPTS;
    }

    private function logRetryScheduled(string $context, int $attempt, string $reason, ?int $statusCode): void
    {
        $this->logger->info(sprintf('Gemini %s retry scheduled.', $context), [
            'model' => $this->model,
            'attempt' => $attempt,
            'next_attempt' => $attempt + 1,
            'max_attempts' => self::MAX_ATTEMPTS,
            'status_code' => $statusCode,
            'retry_reason' => $reason,
            'retry_delay_seconds' => 1,
        ]);
    }

    private function httpFailureMessage(string $context, int $statusCode, string $rawBody): string
    {
        return match ($statusCode) {
            403 => sprintf('Gemini %s request was rejected (403). Check API key validity or API access.', $context),
            404 => sprintf('Gemini %s request failed because the configured Gemini model was not found (404).', $context),
            429 => sprintf('Gemini %s request hit Gemini quota limits (429).', $context),
            503 => sprintf('Gemini %s request failed because Gemini is temporarily unavailable (503).', $context),
            default => trim($rawBody) === ''
                ? sprintf('Gemini %s request returned an empty response body.', $context)
                : sprintf('Gemini %s request returned an unusable response.', $context),
        };
    }

    private function isTimeoutException(string $message): bool
    {
        $message = strtolower($message);

        return str_contains($message, 'timeout')
            || str_contains($message, 'timed out')
            || str_contains($message, 'idle timeout');
    }

    /** @return list<string> */
    private function stringList(mixed $value, int $minimum, int $maximum): array
    {
        if (!is_array($value)) {
            return [];
        }

        $items = [];
        foreach ($value as $item) {
            if (!is_string($item)) {
                continue;
            }

            $item = trim($item);
            if ($item !== '') {
                $items[] = $item;
            }
        }

        $items = array_slice(array_values(array_unique($items)), 0, $maximum);

        return count($items) >= $minimum ? $items : [];
    }

    /**
     * @return array{
     *     focus:string,
     *     foods:list<string>,
     *     dishes:list<string>,
     *     note:string
     * }|null
     */
    private function nutritionSupport(mixed $value): ?array
    {
        if (!is_array($value)) {
            return null;
        }

        $focus = $this->stringValue($value['focus'] ?? null);
        $foods = $this->stringList($value['foods'] ?? null, 3, 5);
        $dishes = $this->stringList($value['dishes'] ?? null, 2, 4);
        $note = $this->stringValue($value['note'] ?? null);

        if ($focus === '' || count($foods) < 3 || count($dishes) < 2 || $note === '') {
            return null;
        }

        return [
            'focus' => $focus,
            'foods' => $foods,
            'dishes' => $dishes,
            'note' => $note,
        ];
    }
}
