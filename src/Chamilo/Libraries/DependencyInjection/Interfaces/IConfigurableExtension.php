<?php
namespace Chamilo\Libraries\DependencyInjection\Interfaces;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * Interface to add an additional method to the dependency injection extensions to make it possible to load a
 * separate configuration file per package.
 * 
 * @package Chamilo\Libraries\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface IConfigurableExtension extends ExtensionInterface
{

    /**
     * Loads the configuration for this package in the container
     * 
     * @param ContainerBuilder $container
     */
    public function loadContainerConfiguration(ContainerBuilder $container);
}