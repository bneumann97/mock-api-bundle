<?php

declare(strict_types=1);

namespace Bneumann\MockApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TestHttpClientCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container->register('Bneumann\MockApiBundle\HttpClient\MockHttpClient', 'Bneumann\MockApiBundle\HttpClient\MockHttpClient')
            ->setDecoratedService('http_client')
            ->addArgument(new Reference('bneumann_mock_api.mock_api_service'));
    }
}
