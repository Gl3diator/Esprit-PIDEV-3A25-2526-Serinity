<?php

namespace App\Tests\Service;

use App\Controller\RapportController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;

final class RapportServiceTest extends TestCase
{
    private function createController(): RapportController
    {
        $controller = new RapportController();

        /**
         * Nécessaire car RapportController hérite de AbstractController.
         * La méthode json() a besoin d'un container.
         */
        $controller->setContainer(new Container());

        return $controller;
    }

    private function createPostRequest(array $data): Request
    {
        return Request::create(
            '/user/consultation/translate',
            'POST',
            $data
        );
    }

    public function testTranslateConsultationReturnsErrorWhenTextIsEmpty(): void
    {
        $controller = $this->createController();

        $request = $this->createPostRequest([
            'text' => '',
            'lang' => 'en',
        ]);

        $httpClient = new MockHttpClient();

        $response = $controller->translateConsultation($request, $httpClient);

        $this->assertSame(200, $response->getStatusCode());

        $data = json_decode(
            $response->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertFalse($data['success']);
        $this->assertSame('Empty text', $data['message']);
    }

    public function testTranslateConsultationReturnsTranslatedText(): void
    {
        $controller = $this->createController();

        $apiResponse = [
            'responseData' => [
                'translatedText' => 'Hello doctor',
            ],
        ];

        $httpClient = new MockHttpClient([
            new MockResponse(
                json_encode($apiResponse, JSON_THROW_ON_ERROR),
                [
                    'http_code' => 200,
                    'response_headers' => [
                        'content-type' => 'application/json',
                    ],
                ]
            ),
        ]);

        $request = $this->createPostRequest([
            'text' => 'Bonjour docteur',
            'lang' => 'en',
        ]);

        $response = $controller->translateConsultation($request, $httpClient);

        $this->assertSame(200, $response->getStatusCode());

        $data = json_decode(
            $response->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertTrue($data['success']);
        $this->assertSame('Hello doctor', $data['translated']);
    }

    public function testTranslateConsultationUsesEnglishAsDefaultLanguage(): void
    {
        $controller = $this->createController();

        $apiResponse = [
            'responseData' => [
                'translatedText' => 'Medical report',
            ],
        ];

        $httpClient = new MockHttpClient([
            new MockResponse(
                json_encode($apiResponse, JSON_THROW_ON_ERROR),
                [
                    'http_code' => 200,
                    'response_headers' => [
                        'content-type' => 'application/json',
                    ],
                ]
            ),
        ]);

        $request = $this->createPostRequest([
            'text' => 'Rapport médical',
        ]);

        $response = $controller->translateConsultation($request, $httpClient);

        $this->assertSame(200, $response->getStatusCode());

        $data = json_decode(
            $response->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertTrue($data['success']);
        $this->assertSame('Medical report', $data['translated']);
    }

    public function testTranslateConsultationReturnsOriginalTextWhenApiFails(): void
    {
        $controller = $this->createController();

        $httpClient = new MockHttpClient(function (): never {
            throw new TransportException('Translation API unavailable');
        });

        $request = $this->createPostRequest([
            'text' => 'Bonjour',
            'lang' => 'en',
        ]);

        $response = $controller->translateConsultation($request, $httpClient);

        $this->assertSame(200, $response->getStatusCode());

        $data = json_decode(
            $response->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertFalse($data['success']);
        $this->assertSame('Bonjour', $data['translated']);
    }
}