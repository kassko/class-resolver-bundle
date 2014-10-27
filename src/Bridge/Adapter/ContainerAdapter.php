<?php

namespace Kassko\ClassResolverBundle\Bridge\Adapter;

use Kassko\ClassResolver\ContainerInterface as ClassResolverContainerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as SymfonyContainerInterface;

/**
 * A container adapter to use class resolver container interface.
 *
 * @author kko
 */
class ContainerAdapter implements ClassResolverContainerInterface
{
    private $symfonyContainer;

    public function __construct(SymfonyContainerInterface $symfonyContainer)
    {
        $this->symfonyContainer = $symfonyContainer;
    }

    public function get($serviceName)
    {
        return $this->symfonyContainer->get($serviceName);
    }
}
