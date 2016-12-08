<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Integration\Chamilo\Core\Home;

use Chamilo\Core\Home\Interfaces\AngularConnectorInterface;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

/**
 * Connector to provide angular modules
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AngularConnector implements AngularConnectorInterface
{

    /**
     * Loads the angular javascript modules and returns them as HTML code
     * 
     * @return string
     */
    public function loadAngularModules()
    {
        return ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->namespaceToFullPath('Chamilo\Core\Repository\ContentObject\RssFeed', true) .
                 'Resources/Javascript/RssFeedRenderer/rssFeedRenderer.js');
    }

    /**
     * Returns a list of angular modules that must be registered
     * 
     * @return string[]
     */
    public function getAngularModules()
    {
        return array('rssFeedRendererApp');
    }
}