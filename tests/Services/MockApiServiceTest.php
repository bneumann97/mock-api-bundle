<?php

declare(strict_types=1);

namespace Bneumann\MockApiBundle\Tests\Services;

use Bneumann\MockApiBundle\Services\MockApiService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Yaml\Yaml;

class MockApiServiceTest extends TestCase
{
    private string $mocksPath;

    /**
     * @var array<string, array<int, array<string, array<string, int|string>|string>>> $mockData
     */
    private array $mockData;

    protected function setUp(): void
    {
        // Set up a temporary directory for mocks
        $this->mocksPath = sys_get_temp_dir() . '/mockapi_mocks';
        if (!is_dir($this->mocksPath)) {
            mkdir($this->mocksPath, 0777, true);
        }

        $this->mockData = [
            'mocks' => [
                [
                    'url' => '/test-url',
                    'method' => 'GET',
                    'request' => [
                        'body' => 'test-body'
                    ],
                    'response' => [
                        'status' => 200,
                        'body' => 'response-body'
                    ]
                ]
            ]
        ];
    }

    protected function tearDown(): void
    {
        // Clean up the temporary directory
        array_map('unlink', glob("$this->mocksPath/*.*"));
        rmdir($this->mocksPath);
    }

    /**
     * @param array<string, array<int, array<string, array<string, int|string>|string>>> $content
     */
    private function createMockFile(string $filename, array $content): void
    {
        file_put_contents($this->mocksPath . '/' . $filename, Yaml::dump($content));
    }

    public function testLoadMocksFromDirectory(): void
    {
        $this->createMockFile('mock1.yaml', $this->mockData);

        $mockApiService = new MockApiService($this->mocksPath);

        $reflection = new ReflectionClass($mockApiService);
        $property = $reflection->getProperty('mocks');
        $property->setAccessible(true);
        $loadedMocks = $property->getValue($mockApiService);

        $this->assertCount(1, $loadedMocks);
        $this->assertEquals($this->mockData['mocks'][0], $loadedMocks[0]);
    }

    public function testFindMock(): void
    {
        $this->createMockFile('mock1.yaml', $this->mockData);
        $mockApiService = new MockApiService($this->mocksPath);

        // Act
        $result = $mockApiService->findMock('/test-url', 'GET', 'test-body');

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($this->mockData['mocks'][0], $result);
    }

    public function testFindMockNotFound(): void
    {
        $this->createMockFile('mock1.yaml', $this->mockData);
        $mockApiService = new MockApiService($this->mocksPath);

        // Act
        $result = $mockApiService->findMock('/wrong-url', 'POST', 'wrong-body');

        // Assert
        $this->assertNull($result);
    }
}
