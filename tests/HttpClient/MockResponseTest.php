<?php

declare(strict_types=1);

namespace Bneumann\MockApiBundle\Tests\HttpClient;

use Bneumann\MockApiBundle\HttpClient\MockResponse;
use PHPUnit\Framework\TestCase;

class MockResponseTest extends TestCase
{
    public function testGetStatusCode(): void
    {
        $info = ['http_code' => 200];
        $response = new MockResponse('{"message": "OK"}', $info);

        $statusCode = $response->getStatusCode();

        $this->assertEquals(200, $statusCode);
    }

    public function testGetHeaders(): void
    {
        $info = ['response_headers' => ['Content-Type' => 'application/json']];
        $response = new MockResponse('{"message": "OK"}', $info);

        $headers = $response->getHeaders();

        $this->assertEquals(['Content-Type' => 'application/json'], $headers);
    }

    public function testGetContent(): void
    {
        $response = new MockResponse('{"message": "OK"}');

        $content = $response->getContent();

        $this->assertEquals('{"message": "OK"}', $content);
    }

    public function testToArray(): void
    {
        $response = new MockResponse('{"message": "OK"}');

        $arrayContent = $response->toArray();

        $this->assertEquals(['message' => 'OK'], $arrayContent);
    }

    public function testCancel(): void
    {
        $response = new MockResponse('{"message": "OK"}');

        $response->cancel();
        $info = $response->getInfo();

        $this->assertTrue($info['canceled']);
    }

    public function testGetInfo(): void
    {
        $info = [
            'http_code' => 200,
            'response_headers' => ['Content-Type' => 'application/json'],
            'url' => 'http://example.com',
            'canceled' => false,
            'error' => null,
            'http_method' => '',
            'redirect_count' => 0,
            'redirect_url' => null,
            'start_time' => 0.0,
            'user_data' => null,
            'peer_certificate_chain' => null,
        ];
        $response = new MockResponse('{"message": "OK"}', $info);

        $this->assertEquals(200, $response->getInfo('http_code'));
        $this->assertEquals(['Content-Type' => 'application/json'], $response->getInfo('response_headers'));
        $this->assertEquals('http://example.com', $response->getInfo('url'));
        $this->assertEquals($info, $response->getInfo());
    }
}
