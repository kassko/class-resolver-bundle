<?php

use Kassko\Bundle\ClassResolverBundle\DependencyInjection\Compiler\InjectClassResolverPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class InjectClassResolversPassTest extends \PHPUnit_Framework_TestCase
{
    private static $container;

    public static function setUpBeforeClass()
    {
        self::$container = self::createContainer();
    }

    public function testInjectClassResolverPass()
    {
        $container = self::$container;

        //someServiceA
        $someServiceA = $container->getDefinition('some_serviceA');
        $this->assertEquals($someServiceA->getArgument(0), 'kassko_class_resolver.chain');

        //someServiceB
        $this->assertTrue(
            $container->hasDefinition('kassko_class_resolver.chain.some_group'),
            '->configure pass adds a class resolver service for tagged service'
        );

        $someServiceB = $container->getDefinition('some_serviceB');
        $this->assertEquals($someServiceB->getArgument(0), 'kassko_class_resolver.chain.some_group');

        //someServiceC
        $this->assertTrue(
            $container->hasDefinition('kassko_class_resolver.chain.some_group'),
            '->configure pass adds a class resolver service for tagged service'
        );

        $someServiceC = $container->getDefinition('some_serviceC');
        $this->assertEquals($someServiceC->getArgument(1), 'kassko_class_resolver.chain.some_group');
    }

    private static function createContainer()
    {
        $container = new ContainerBuilder();
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../../Resources/config')
        );
        $loader->load('services.xml');

        $someService = new Definition('SomeClass', [new Reference('class_resolver')]);
        $someService->addTag('kassko_class_resolver.inject');
        $container->setDefinition('some_serviceA', $someService);

        $someService = new Definition('SomeClass', [new Reference('class_resolver')]);
        $someService->addTag('kassko_class_resolver.inject', ['group' => 'some_group']);
        $container->setDefinition('some_serviceB', $someService);

        $someService = new Definition('SomeClass', ['argument0', new Reference('class_resolver')]);
        $someService->addTag('kassko_class_resolver.inject', ['group' => 'some_group', 'index' => 1]);
        $container->setDefinition('some_serviceC', $someService);

        $container->addCompilerPass(new InjectClassResolverPass());
        $container->compile();

        return $container;
    }
}