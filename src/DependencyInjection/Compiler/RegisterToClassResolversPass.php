<?php

namespace Kassko\Bundle\ClassResolverBundle\DependencyInjection\Compiler;

use LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class RegisterToClassResolversPass implements CompilerPassInterface
{
    use ConfigureClassResolversTrait;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach (
            $container->findTaggedServiceIds(self::$classesToRegisterTag)
            as
            $serviceId => $tagAttributes
        ) {

            foreach ($tagAttributes as $attributes) {

                list($classResolverPrototypeId, $group) = $this->computeClassResolverId($attributes);
                $classResolverId = $this->getClassResolverIdWithGroup($classResolverPrototypeId, $group);

                if ($container->hasDefinition($classResolverId)) {

                    $classResolverDef = $container->getDefinition($classResolverId);
                    $classResolverChainDef = $container->getDefinition(
                        $this->getClassResolverIdWithGroup('kassko_class_resolver.chain', $group)
                    );
                } else {

                    $classResolverDef = new DefinitionDecorator($classResolverPrototypeId);
                    $container->setDefinition($classResolverId, $classResolverDef);

                    $classResolverChainDef = new DefinitionDecorator('kassko_class_resolver.chain');
                    $container->setDefinition(
                        $this->getClassResolverIdWithGroup('kassko_class_resolver.chain', $group),
                        $classResolverChainDef
                    );

                    $classResolverChainDef->addMethodCall('add', [new Reference($classResolverId)]);
                }

                $class = $container->getDefinition($serviceId)->getClass();
                if (empty($class)) {
                    throw new LogicException(
                        sprintf("No class defined for service '%s'.", $serviceId)
                    );
                }

                $classResolverDef->addMethodCall('registerClass', [$class, $serviceId]);
            }
        }
    }
}