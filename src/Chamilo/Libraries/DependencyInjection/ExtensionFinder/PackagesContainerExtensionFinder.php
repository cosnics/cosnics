<?php
namespace Chamilo\Libraries\DependencyInjection\ExtensionFinder;

use Chamilo\Libraries\DependencyInjection\Interfaces\ContainerExtensionFinderInterface;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesClassFinderAware;

/**
 * Finds dependency injection extensions in a given list of packages
 * 
 * @package Chamilo\Libraries\DependencyInjection\ExtensionFinder
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PackagesContainerExtensionFinder extends PackagesClassFinderAware implements ContainerExtensionFinderInterface
{

    /**
     * Locates the container extension classes
     * 
     * @return string[]
     */
    public function findContainerExtensions()
    {
        return $this->getPackagesClassFinder()->findClasses(
            'DependencyInjection/DependencyInjectionExtension.php', 
            'DependencyInjection\\DependencyInjectionExtension');
    }
}