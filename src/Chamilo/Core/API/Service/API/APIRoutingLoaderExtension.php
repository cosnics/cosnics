<?php
namespace Chamilo\Core\API\Service\API;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class APIRoutingLoaderExtension implements \Chamilo\Core\API\Service\Architecture\Routing\APIRoutingLoaderExtensionInterface
{
    public function load(RouteCollection $collection): void
    {
        $pathBuilder = new PathBuilder(new ClassnameUtilities(new StringUtilities()));

        $loader = new \Symfony\Component\Routing\Loader\XmlFileLoader(
            new FileLocator($pathBuilder->getConfigurationPath('Chamilo\Core\API') . 'APIRouting')
        );

        $collection->addCollection($loader->load('routes.xml'));
    }
}