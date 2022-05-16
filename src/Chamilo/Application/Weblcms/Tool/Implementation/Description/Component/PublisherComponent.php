<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Description\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Description\Manager;

/**
 *
 * @package application.lib.weblcms.tool.description.component
 */
class PublisherComponent extends Manager
{

    public function get_additional_parameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Core\Repository\Viewer\Manager::PARAM_ID;
        $additionalParameters[] = \Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION;
        $additionalParameters[] = \Chamilo\Core\Repository\Viewer\Manager::PARAM_IN_WORKSPACES;
        $additionalParameters[] = \Chamilo\Core\Repository\Viewer\Manager::PARAM_WORKSPACE_ID;

        return $additionalParameters;
    }
}
