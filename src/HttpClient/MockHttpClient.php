<?php

declare(strict_types=1);

namespace Bneumann\MockApiBundle\HttpClient;

use Bneumann\MockApiBundle\Services\MockApiService;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\HttpClientTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

class MockHttpClient implements HttpClientInterface
{
    use HttpClientTrait;

    /**
     * @var array<string, mixed>
     */
    private array $defaultOptions = [];

    public function __construct(
        private MockApiService $mockApiService,
        private ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClient::create();
    }

    /**
     * @param array<string, mixed> $options
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $body = $options['body'] ?? '';

        $mock = $this->mockApiService->findMock($url, $method, $body);

        if ($mock) {
            $info = [
                'http_code' => $mock['response']['status'],
                'response_headers' => $mock['response']['headers'],
            ];

            $content = json_encode($mock['response']['body']) ?: '';

            return new MockResponse($content, $info);
        }

        return $this->httpClient->request($method, $url, $options);
    }

    public function stream(iterable|ResponseInterface $responses, ?float $timeout = null): ResponseStreamInterface
    {
        return $this->httpClient->stream($responses, $timeout);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function withOptions(array $options): static
    {
        $clone = clone $this;
        $clone->defaultOptions = self::mergeDefaultOptions($options, $this->defaultOptions, true);

        return $clone;
    }
}
