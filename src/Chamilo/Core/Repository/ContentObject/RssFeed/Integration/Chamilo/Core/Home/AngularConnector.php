<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Integration\Chamilo\Core\Home;

use Chamilo\Core\Home\Interfaces\AngularConnectorInterface;
use Chamilo\Core\Repository\ContentObject\RssFeed\Storage\DataClass\RssFeed;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

/**
 * Connector to provide angular modules
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AngularConnector implements AngularConnectorInterface
{

    /**
     * Returns a list of angular modules that must be registered
     *
     * @return string[]
     */
    public function getAngularModules()
    {
        return ['rssFeedRendererApp'];
    }

    /**
     * Loads the angular javascript modules and returns them as HTML code
     *
     * @return string
     */
    public function loadAngularModules()
    {
        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();

        /**
         * @var \Chamilo\Libraries\Format\Utilities\ResourceManager $resourceManager
         */
        $resourceManager = $container->get(ResourceManager::class);

        /**
         * @var \Chamilo\Libraries\File\WebPathBuilder $webPathBuilder
         */
        $webPathBuilder = $container->get(WebPathBuilder::class);

        return $resourceManager->getResourceHtml(
            $webPathBuilder->getJavascriptPath(RssFeed::CONTEXT) . 'RssFeedRenderer/rssFeedRenderer.js'
        );
    }
}