<?php

namespace Kassko\Bundle\ClassResolverBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class KasskoClassResolverExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setAlias('kassko_class_resolver', new Alias('kassko_class_resolver.chain', true));

        $containerAdapterDef = $container->getDefinition('kassko_class_resolver.container');
        $containerAdapterDef->setClass($config['container_adapter_class']);
    }
}
