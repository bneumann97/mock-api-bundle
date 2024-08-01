<?php

declare(strict_types=1);

namespace Bneumann\MockApiBundle\Services;

use Symfony\Component\Yaml\Yaml;

class MockApiService
{
    /**
     * @param array<string, mixed> $mocks
     */
    public function __construct(
        private string $mocksPath,
        private array $mocks = [],
    ) {
        $this->loadMocksFromDirectory();
    }

    private function loadMocksFromDirectory(): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->mocksPath)
        );
        foreach ($files as $file) {
            if ($file->isFile() && 'yaml' === $file->getExtension()) {
                $file = Yaml::parseFile($file->getPathname());

                if (isset($file['mocks'])) {
                    $this->mocks = array_merge($this->mocks, $file['mocks']);
                }
            }
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findMock(string $url, string $method, string $body): ?array
    {
        foreach ($this->mocks as $mock) {
            if ($mock['url'] === $url && $mock['method'] === strtoupper($method)) {
                if (isset($mock['request']['body']) && $mock['request']['body'] !== $body) {
                    continue;
                }

                return $mock;
            }
        }

        return null;
    }
}
