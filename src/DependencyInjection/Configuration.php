<?php

namespace Kassko\Bundle\ClassResolverBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $rootNode = $builder->root('kassko_class_resolver');

        $rootNode
            ->children()
            ->scalarNode('container_adapter_class')->defaultValue('Kassko\Bundle\ClassResolverBundle\Adapter\Container\SymfonyContainerAdapter')
        ->end();

        return $builder;
    }
}
