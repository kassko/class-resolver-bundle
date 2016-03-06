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
                if (isset($attributes['service'])) {
                    if (! $container->hasDefinition($attributes['service'])) {
                        throw new LogicException(
                            sprintf(
                                'The service "%s" does not exist.'
                                . ' The definition of service "%s" has a tag "%s"'
                                . ' and the attribute "service" of this tag contains this unexisting service.',
                                $attributes['service'],
                                $serviceId,
                                self::$classesToRegisterTag
                            )
                        );
                    }

                    $serviceClass = $container->getDefinition($serviceId)->getClass();
                    if (empty($serviceClass)) {
                        throw new LogicException(
                            sprintf('No class defined for service "%s".', $serviceId)
                        );
                    }

                    $classResolverDef = $container->getDefinition($attributes['service']);
                    $classResolverDef->addMethodCall('registerClass', [$serviceClass, $serviceId]);
                    
                    continue;
                }

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

                $serviceClass = $container->getDefinition($serviceId)->getClass();
                if (empty($serviceClass)) {
                    throw new LogicException(
                        sprintf('No class defined for service "%s".', $serviceId)
                    );
                }

                $classResolverDef->addMethodCall('registerClass', [$serviceClass, $serviceId]);
            }
        }
    }
}
