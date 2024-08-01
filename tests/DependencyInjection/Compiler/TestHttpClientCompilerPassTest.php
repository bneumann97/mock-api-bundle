<?php

declare(strict_types=1);

namespace Bneumann\MockApiBundle\Tests\DependencyInjection\Compiler;

use Bneumann\MockApiBundle\DependencyInjection\Compiler\TestHttpClientCompilerPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class TestHttpClientCompilerPassTest extends TestCase
{
    public function testProcess(): void
    {
        $container = new ContainerBuilder();
        $compilerPass = new TestHttpClientCompilerPass();

        // Register a mock service that the MockHttpClient depends on
        $mockApiServiceDefinition = new Definition();
        $container->setDefinition('bneumann_mock_api.mock_api_service', $mockApiServiceDefinition);

        $compilerPass->process($container);

        $this->assertTrue($container->hasDefinition('Bneumann\MockApiBundle\HttpClient\MockHttpClient'));

        $mockHttpClientDefinition = $container->getDefinition('Bneumann\MockApiBundle\HttpClient\MockHttpClient');
        $this->assertEquals('Bneumann\MockApiBundle\HttpClient\MockHttpClient', $mockHttpClientDefinition->getClass());
        $this->assertCount(1, $mockHttpClientDefinition->getArguments());
        $this->assertEquals(new Reference('bneumann_mock_api.mock_api_service'), $mockHttpClientDefinition->getArgument(0));
        $this->assertEquals('http_client', $mockHttpClientDefinition->getDecoratedService()[0]);
    }
}
