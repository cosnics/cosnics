<?php
namespace Chamilo\Libraries\DependencyInjection\Interfaces;

/**
 * An interface to describe a container extension finder necessary to find the DependencyInjection classes
 * Interface ContainerExtensionFinderInterface
 *
 * @package Chamilo\Libraries\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ContainerExtensionFinderInterface
{

    /**
     * Locates the container extension classes
     *
     * @return string[]
     */
    public function findContainerExtensions();
}