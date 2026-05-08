<?php

namespace App\Tests\Service;

use App\Controller\RapportController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;

final class RapportServiceTest extends TestCase
{
    private function createController(): RapportController
    {
        $controller = new RapportController();
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
}
