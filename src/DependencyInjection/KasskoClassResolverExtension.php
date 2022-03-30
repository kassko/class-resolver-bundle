<?php

namespace Kassko\Bundle\ClassResolverBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class KasskoClassResolverExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setAlias('kassko_class_resolver', new Alias('kassko_class_resolver.chain', true));

        $containerAdapterDef = $container->getDefinition('kassko_class_resolver.container');
        $containerAdapterDef->setClass($config['container_adapter_class']);
        $container->addAliases($config['resolvers_aliases']);

        if (isset($config['resolvers'])) {

            foreach ($config['resolvers']['chain'] as $resolverConfig) {
                $resolverDef = new ChildDefinition('kassko_class_resolver.chain');

                foreach ($resolverConfig['nested_resolvers'] as $nestedResolverService) {
                    $resolverDef->addMethodCall('add', [new Reference($nestedResolverService)]);
                }

                if (null !== $resolverConfig['service']) {
                    $resolverDef->addMethodCall('setDefault', [new Reference($resolverConfig['service'])]);
                } else {
                    $defaultResolverDef = new Definition($container->getParameter('kassko_class_resolver.default.class'), ['action' => $resolverConfig['on_not_found_class']['action']]);
                    $resolverDef->addMethodCall('setDefault', [$defaultResolverDef]);
                }
                

                $this->addAliasesByService($container, $config['resolver_aliases'], $resolverConfig['resolver_service']);
            }

            foreach ($config['resolvers']['container'] as $resolverConfig) {
                $container->setDefinition($resolverConfig['resolver_service'], new ChildDefinition('kassko_class_resolver.container_aware'));
                $this->addAliasesByService($container, $resolverConfig['resolver_aliases'], $resolverConfig['resolver_service']);
            }

            foreach ($config['resolvers']['map'] as $resolverConfig) {
                foreach ($resolverConfig['items'] as $item) {
                    if ('@' !== substr($item['service'], 0, 1)) {
                        $item['service'] = new Reference($item['service']);
                    }
                }
                $resolverDef = new Definition($container->getParameter('kassko_class_resolver.map.class'), [$resolverConfig['items']]);
                $container->setDefinition($resolverConfig['resolver_service'], $resolverDef);
                $this->addAliasesByService($container, $resolverConfig['resolver_aliases'], $resolverConfig['resolver_service']);
            }

            foreach ($config['resolvers']['factory_adapter'] as $index => $resolverConfig) {
                $resolverDef = new Definition(
                    $container->getParameter('kassko_class_resolver.factory_adapter.class'),
                    [new Reference($resolverConfig['adapted_factory']), $resolverConfig['support_method'], $resolverConfig['resolve_method']]
                );
                $container->setDefinition($resolverConfig['resolver_service'], $resolverDef);
                $this->addAliasesByService($container, $resolverConfig['resolver_aliases'], $resolverConfig['resolver_service']);
            }

            foreach ($config['resolvers']['static_factory_adapter'] as $index => $resolverConfig) {
                $resolverDef = new Definition(
                    $container->getParameter('kassko_class_resolver.static_factory_adapter.class'),
                    [$resolverConfig['adapted_factory'], $resolverConfig['support_method'], $resolverConfig['resolve_method']]
                );
                $container->setDefinition($resolverConfig['resolver_service'], $resolverDef);
                $this->addAliasesByService($container, $resolverConfig['resolver_aliases'], $resolverConfig['resolver_service']);
            }
        }
    }

    /**
     * @param ContainerBuilder      $container
     * @param array                 $aliases
     * @param string                $service
     */
    protected function addAliasesByService(ContainerBuilder $container, array $aliases, $service)
    {
        foreach ($aliases as $alias) {
            $container->setAlias($alias, $service);
        }
    }
}
