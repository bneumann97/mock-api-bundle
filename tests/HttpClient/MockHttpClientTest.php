<?php

declare(strict_types=1);

namespace Bneumann\MockApiBundle\Tests\HttpClient;

use Bneumann\MockApiBundle\HttpClient\MockHttpClient;
use Bneumann\MockApiBundle\HttpClient\MockResponse;
use Bneumann\MockApiBundle\Services\MockApiService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MockHttpClientTest extends TestCase
{
    public function testRequestWithMock(): void
    {
        // Arrange
        $mockApiService = $this->createMock(MockApiService::class);
        $httpClient = $this->createMock(HttpClientInterface::class);
        $mock = [
            'url' => '/test-url',
            'method' => 'GET',
            'request' => ['body' => ''],
            'response' => [
                'status' => 200,
                'headers' => ['Content-Type' => 'application/json'],
                'body' => ['message' => 'mocked response']
            ]
        ];

        $mockApiService->method('findMock')->willReturn($mock);

        $client = new MockHttpClient($mockApiService, $httpClient);

        // Act
        $response = $client->request('GET', '/test-url');

        // Assert
        $this->assertInstanceOf(MockResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['message' => 'mocked response'], json_decode($response->getContent(), true));
        $this->assertEquals(['Content-Type' => 'application/json'], $response->getHeaders());
    }

    public function testRequestWithoutMock(): void
    {
        $mockApiService = $this->createMock(MockApiService::class);
        $httpClient = $this->createMock(HttpClientInterface::class);

        $mockApiService->method('findMock')->willReturn(null);

        $mockResponse = $this->createMock(ResponseInterface::class);
        $httpClient->method('request')->willReturn($mockResponse);

        $client = new MockHttpClient($mockApiService, $httpClient);

        $response = $client->request('GET', 'http://example.com/test-url');

        $this->assertSame($mockResponse, $response);
    }
}
