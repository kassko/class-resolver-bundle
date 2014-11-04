<?php

namespace Kassko\Bundle\ClassResolverBundle\DependencyInjection\Compiler;

trait ConfigureClassResolversTrait
{
    private static $classesToRegisterTag = 'kassko_class_resolver.add';
    private static $classesInWhichInjectTag = 'kassko_class_resolver.inject';

    private function computeClassResolverId(array $attributes)
    {
        if (! isset($attributes['factory']) || false === $attributes['factory']) {
            $classResolverPrototypeId = 'kassko_class_resolver.container_aware';
        } else {
            $classResolverPrototypeId = 'kassko_class_resolver.factory';
        }

        if (! isset($attributes['group'])) {
            return [$classResolverPrototypeId, ''];
        }

        return [$classResolverPrototypeId, $attributes['group']];
    }

    private function getClassResolverIdWithGroup($classResolverId, $group)
    {
        return $classResolverId.'.'.$group;
    }
}