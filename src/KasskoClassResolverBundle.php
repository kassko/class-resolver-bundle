<?php

namespace Kassko\Bundle\ClassResolverBundle;

use Kassko\Bundle\ClassResolverBundle\DependencyInjection\Compiler\InjectClassResolversPass;
use Kassko\Bundle\ClassResolverBundle\DependencyInjection\Compiler\RegisterToClassResolversPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KasskoClassResolverBundle extends Bundle
{
	/**
     * {@inheritdoc}
     */
	public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container
            ->addCompilerPass(new InjectClassResolversPass())
            ->addCompilerPass(new RegisterToClassResolversPass())
        ;
    }
}
