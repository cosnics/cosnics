<?php
namespace Chamilo\Libraries\DependencyInjection\Service;

use Chamilo\Libraries\DependencyInjection\Architecture\Interface\ContainerExtensionFinderInterface;
use Chamilo\Libraries\Filesystem\Service\PackagesContentFinder\PackagesClassFinderAware;

/**
 * @package Chamilo\Libraries\DependencyInjection\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PackagesContainerExtensionFinder extends PackagesClassFinderAware implements ContainerExtensionFinderInterface
{
    /**
     * @return string[]
     * @throws \Exception
     */
    public function findContainerExtensions(): array
    {
        return $this->packagesClassFinder->findClasses(
            'DependencyInjection/DependencyInjectionExtension.php', 'DependencyInjection\\DependencyInjectionExtension'
        );
    }
}