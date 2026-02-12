<?php
namespace Chamilo\Libraries\DependencyInjection\Architecture\Interface;

/**
 * An interface to describe a container extension finder necessary to find the DependencyInjection classes
 * Interface ContainerExtensionFinderInterface
 *
 * @package Chamilo\Libraries\DependencyInjection\Architecture\Interface
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ContainerExtensionFinderInterface
{

    /**
     * @return string[]
     */
    public function findContainerExtensions(): array;
}