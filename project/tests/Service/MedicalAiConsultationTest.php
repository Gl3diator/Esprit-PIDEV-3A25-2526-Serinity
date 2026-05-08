<?php

namespace App\Tests\Service;

use App\Controller\MedicalAiConsultation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;

final class MedicalAIConsultationTest extends TestCase
{
    protected function setUp(): void
    {
        $_ENV['OPENROUTER_API_KEY'] = 'test-api-key';
        $_SERVER['OPENROUTER_API_KEY'] = 'test-api-key';
    }

    private function createController(): MedicalAiConsultation
    {
        $controller = new MedicalAiConsultation();
        $controller->setContainer(new Container());

        return $controller;
    }

    private function createJsonRequest(array $payload): Request
    {
        return Request::create(
            '/user/medical-ai/analyse',
            'POST',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode($payload, JSON_THROW_ON_ERROR)
        );
    }

    public function testAnalyseReturnsAiJsonResponse(): void
    {
        $controller = $this->createController();

        $aiJson = [
            'success' => true,
            'level' => 'medium',
            'title' => 'Possible fever',
            'analysis' => 'The symptoms may indicate a moderate medical issue.',
            'actions' => [
                'Rest',
                'Drink water',
                'Contact a doctor if symptoms continue',
            ],
            'needDoctor' => true,
            'needEmergency' => false,
        ];

        $openRouterResponse = [
            'choices' => [
                [
                    'message' => [
                        'content' => json_encode($aiJson, JSON_THROW_ON_ERROR),
                    ],
                ],
            ],
        ];

        $httpClient = new MockHttpClient([
            new MockResponse(
                json_encode($openRouterResponse, JSON_THROW_ON_ERROR),
                [
                    'http_code' => 200,
                    'response_headers' => [
                        'content-type' => 'application/json',
                    ],
                ]
            ),
        ]);

        $request = $this->createJsonRequest([
            'message' => 'I have fever and headache',
        ]);

        $response = $controller->analyse($request, $httpClient);

        $this->assertSame(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertTrue($data['success']);
        $this->assertSame('medium', $data['level']);
        $this->assertSame('Possible fever', $data['title']);
        $this->assertSame('The symptoms may indicate a moderate medical issue.', $data['analysis']);
        $this->assertTrue($data['needDoctor']);
        $this->assertFalse($data['needEmergency']);
        $this->assertCount(3, $data['actions']);
    }
}
