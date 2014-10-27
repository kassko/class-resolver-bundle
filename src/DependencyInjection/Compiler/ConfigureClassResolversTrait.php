<?php

namespace Kassko\Bundle\ClassResolverBundle\DependencyInjection\Compiler;

trait ConfigureClassResolversTrait
{
    private static $classesToRegisterTag = 'class_resolver.add';
    private static $classesInWhichInjectTag = 'class_resolver.inject';

    private function computeClassResolverId(array $attributes)
    {
        if (! isset($attributes['factory']) || false === $attributes['factory']) {
            $classResolverPrototypeId = 'class_resolver.container_aware';
        } else {
            $classResolverPrototypeId = 'class_resolver.factory';
        }

        if (! isset($attributes['group'])) {
            return [$classResolverPrototypeId, ''];
        }

        return [$classResolverPrototypeId, $attributes['group']];
    }
}