<?php

declare(strict_types=1);

namespace Bneumann\MockApiBundle;

use Bneumann\MockApiBundle\DependencyInjection\Compiler\TestHttpClientCompilerPass;
use Bneumann\MockApiBundle\Services\MockApiService;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class BneumannMockApiBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('mocks_path')->defaultValue('tests/mocks')->end()
                ->end()
            ->end()
        ;
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        if ('test' !== $container->getParameter('kernel.environment')) {
            return;
        }

        $container->addCompilerPass(new TestHttpClientCompilerPass());
    }

    /**
     * @param array<string, mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->services()
            ->set('bneumann_mock_api.mock_api_service', MockApiService::class)
            ->args([
                '$mocksPath' => $config['mocks_path'],
            ])
        ;
    }
}
