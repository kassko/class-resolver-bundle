<?php

namespace Kassko\Bundle\ClassResolverBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        list($rootNode, $builder) = $this->getRootNode('kassko_class_resolver');

        $rootNode
            ->children()
            ->scalarNode('container_adapter_class')->defaultValue('Kassko\Bundle\ClassResolverBundle\Adapter\Container\SymfonyContainerAdapter')->end()
            ->arrayNode('resolvers_aliases')->prototype('scalar')->end()->end()
            ->arrayNode('resolvers')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('chain')
                        ->prototype('array')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('nested_resolvers')->prototype('scalar')->end()->end()
                                ->arrayNode('on_not_found_class')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->enumNode('action')
                                            ->values(['throw_exception', 'be_silent'])
                                            ->defaultValue('be_silent')
                                        ->end()
                                        ->scalarNode('service')->defaultNull()->end()
                                    ->end()
                                ->end()
                                ->scalarNode('resolver_service')->isRequired()->cannotBeEmpty()->end()
                                ->arrayNode('resolver_aliases')->prototype('scalar')->end()->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('container')
                        ->prototype('array')
                            ->beforeNormalization()
                            ->ifTrue(
                                function ($v) {
                                    return !is_array($v);
                                }
                            )
                            ->then(
                                function ($v) {
                                    return ['resolver_service' => $v, 'resolver_aliases' => []];
                                })
                            ->end()
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('resolver_service')->isRequired()->cannotBeEmpty()->end()
                                ->arrayNode('resolver_aliases')->prototype('scalar')->end()->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('map')
                        ->prototype('array')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('resolver_service')->isRequired()->cannotBeEmpty()->end()
                                ->arrayNode('resolver_aliases')->prototype('scalar')->end()->end()
                                ->arrayNode('items')
                                    ->prototype('array')
                                        ->beforeNormalization()
                                        ->ifTrue(
                                            function ($v) {
                                                return 1 === count($v);
                                            }
                                        )
                                        ->then(
                                            function ($v) {
                                                return ['class' => key($v), 'service' => current($v)];
                                            })
                                        ->end()
                                        ->children()
                                            ->scalarNode('class')->isRequired()->cannotBeEmpty()->end()
                                            ->scalarNode('service')->isRequired()->cannotBeEmpty()->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('factory_adapter')
                        ->prototype('array')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('resolver_service')->isRequired()->cannotBeEmpty()->end()
                                ->arrayNode('resolver_aliases')->prototype('scalar')->end()->end()
                                ->scalarNode('adapted_factory')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('support_method')->defaultValue('supports')->cannotBeEmpty()->end()
                                ->scalarNode('resolve_method')->defaultValue('resolve')->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('static_factory_adapter')
                        ->prototype('array')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('resolver_service')->isRequired()->cannotBeEmpty()->end()
                                ->arrayNode('resolver_aliases')->prototype('scalar')->end()->end()
                                ->scalarNode('adapted_factory')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('support_method')->defaultValue('supports')->cannotBeEmpty()->end()
                                ->scalarNode('resolve_method')->defaultValue('resolve')->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $builder;
    }

    private function getRootNode($rootNodeName)
    {
        if (method_exists(TreeBuilder::class, 'getRootNode')) {
            $builder = new TreeBuilder($rootNodeName);
            $rootNode = $builder->getRootNode();
        } else {//Keep compatibility with Symfony <= 4.3
            /**
             * @see https://github.com/symfony/symfony/blob/4.3/src/Symfony/Component/Config/Definition/Builder/TreeBuilder.php#L48
             */
            $builder = new TreeBuilder;
            $rootNode = $builder->root($rootNodeName);
        }

        return [$rootNode, $builder];
    }
}
