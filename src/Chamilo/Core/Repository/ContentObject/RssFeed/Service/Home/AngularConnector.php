<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Service\Home;

use Chamilo\Core\Home\Architecture\Interfaces\AngularConnectorInterface;
use Chamilo\Core\Repository\ContentObject\RssFeed\Storage\DataClass\RssFeed;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

/**
 * Connector to provide angular modules
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AngularConnector implements AngularConnectorInterface
{

    protected ResourceManager $resourceManager;

    protected WebPathBuilder $webPathBuilder;

    public function __construct(ResourceManager $resourceManager, WebPathBuilder $webPathBuilder)
    {
        $this->resourceManager = $resourceManager;
        $this->webPathBuilder = $webPathBuilder;
    }

    /**
     * @return string[]
     */
    public function getAngularModules(): array
    {
        return ['rssFeedRendererApp'];
    }

    public function getResourceManager(): ResourceManager
    {
        return $this->resourceManager;
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }

    public function loadAngularModules(): string
    {
        return $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath(RssFeed::CONTEXT) . 'RssFeedRenderer/rssFeedRenderer.js'
        );
    }
}