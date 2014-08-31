<?php

namespace Kassko\Bundle\ClassResolverBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use LogicException;

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

                list($classResolverPrototypeId, $group) =
                    $this->computeClassResolverId($attributes);

                $classResolverId = $classResolverPrototypeId.$group;

                if ($container->hasDefinition($classResolverId)) {

                    $classResolverDef = $container->getDefinition($classResolverId);
                    $classResolverChainDef = $container->getDefinition(
                        'class_resolver.chain'.$group
                    );
                } else {

                    $classResolverDef =
                        new DefinitionDecorator($classResolverPrototypeId);
                    $container->setDefinition($classResolverId, $classResolverDef);

                    $classResolverChainDef = new DefinitionDecorator(
                        $container->getParameter('class_resolver.chain.class')
                    );
                    $container->setDefinition(
                        'class_resolver.chain'.$group,
                        $classResolverChainDef
                    );
                }

                $class = $container->getDefinition($serviceId)->getClass();
                if (empty($class)) {
                    throw new LogicException(
                        sprintf("No class defined for service '%s'.", $serviceId)
                    );
                }

                $classResolverDef->addMethodCall('registerClass', [$class, $serviceId]);
                $classResolverChainDef->addMethodCall('add', [$classResolverDef]);
            }
        }
    }
}