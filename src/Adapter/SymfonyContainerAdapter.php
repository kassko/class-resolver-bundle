<?php

namespace Kassko\Bundle\ClassResolverBundle\Adapter;

use Kassko\Common\DependencyInjection\ContainerInterface as KasskoContainerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as SymfonyContainerInterface;

/**
 * A container adapter to use the Kassko container interface with a Symfony container implementation.
 *
 * @author kko
 */
class SymfonyContainerAdapter implements KasskoContainerInterface
{
    private $symfonyContainer;

    public function __construct(SymfonyContainerInterface $symfonyContainer)
    {
        $this->symfonyContainer = $symfonyContainer;
    }

    /**
     * {@inheritdoc}
     */
    public function get($serviceName)
    {
        return $this->symfonyContainer->get($serviceName);
    }
}
