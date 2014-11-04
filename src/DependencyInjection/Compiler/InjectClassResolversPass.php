<?php

namespace Kassko\Bundle\ClassResolverBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use OutOfBoundsException;

class InjectClassResolversPass implements CompilerPassInterface
{
    use ConfigureClassResolversTrait;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach (
            $container->findTaggedServiceIds(self::$classesInWhichInjectTag)
            as
            $serviceIdInWhichInjectDef => $tagAttributes
        ) {
            $serviceInWhichInjectDef =
            $container->getDefinition($serviceIdInWhichInjectDef);

            foreach ($tagAttributes as $attributes) {

                $indexArgument = $this->injectClassResolverInPlaceHolder(
                    $attributes,
                    $serviceInWhichInjectDef,
                    $serviceIdInWhichInjectDef
                );
            }
        }
    }

    private function injectClassResolverInPlaceHolder(
        array $attributes,
        Definition $serviceInWhichInjectDef,
        $serviceIdInWhichInjectDef
    ) {
        list(, $group) = $this->computeClassResolverId($attributes);
        $classResolverId = $this->getClassResolverIdWithGroup('kassko_class_resolver.chain', $group);

        $index = isset($attributes['index']) ? $attributes['index'] : 0;

        if (! isset($attributes['method'])) {

            $serviceInWhichInjectDef->replaceArgument($index, new Reference($classResolverId));
        } else {

            $method = $attributes['method'];
            $calls = $serviceInWhichInjectDef->getMethodCalls();

            foreach ($calls as &$call) {
                if ($method === $call[0]) {

                    if (! isset($call[1][$index])) {
                        throw new OutOfBoundsException(
                            sprintf(
                                'The index attribute "%s" for tag "%s"'
                                .' and service "%s" is not in the range [0, %d].',
                                $index,
                                $classesToRegisterTag,
                                $serviceIdInWhichInjectDef,
                                count($calls) - 1
                            )
                        );
                    }
                    $call[1][$index] = new Reference($classResolverId);
                }
            }
            unset($call);//Precaution.

            $serviceInWhichInjectDef->setMethodCalls($calls);
        }
    }
}