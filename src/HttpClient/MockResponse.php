<?php

declare(strict_types=1);

namespace Bneumann\MockApiBundle\HttpClient;

use Symfony\Contracts\HttpClient\ResponseInterface;

class MockResponse implements ResponseInterface
{
    /**
     * @param array<string, mixed> $info
     */
    public function __construct(
        private string $content,
        private array $info = [],
    ) {
        $this->info = array_merge([
            'canceled' => false,
            'error' => null,
            'http_code' => 0,
            'http_method' => '',
            'redirect_count' => 0,
            'redirect_url' => null,
            'response_headers' => [],
            'start_time' => 0.0,
            'url' => '',
            'user_data' => null,
            'peer_certificate_chain' => null,
        ], $info);
    }

    public function getStatusCode(): int
    {
        return $this->info['http_code'];
    }

    public function getHeaders(bool $throw = true): array
    {
        return $this->info['response_headers'];
    }

    public function getContent(bool $throw = true): string
    {
        return $this->content;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(bool $throw = true): array
    {
        return json_decode($this->content, true);
    }

    public function cancel(): void
    {
        $this->info['canceled'] = true;
    }

    public function getInfo(?string $type = null): mixed
    {
        if ($type) {
            return $this->info[$type] ?? null;
        }

        return $this->info;
    }
}
