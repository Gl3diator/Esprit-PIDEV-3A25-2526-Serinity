<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TranslationService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiKey = '',
    ) {
    }

    public function translate(string $text, string $targetLanguage): string
    {
        if (trim($text) === '') {
            return '';
        }

        if ($this->apiKey === '') {
            return sprintf('[translation disabled] %s', $text);
        }

        $response = $this->httpClient->request('POST', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key='.$this->apiKey, [
            'json' => [
                'contents' => [[
                    'parts' => [[
                        'text' => sprintf('Translate this to %s and return only the translated text: %s', $targetLanguage, $text),
                    ]],
                ]],
            ],
        ]);

        $data = $response->toArray(false);

        return $data['candidates'][0]['content']['parts'][0]['text'] ?? $text;
    }
}
