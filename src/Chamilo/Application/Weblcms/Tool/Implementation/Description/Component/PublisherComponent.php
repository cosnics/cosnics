<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Description\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Description\Manager;

/**
 *
 * @package application.lib.weblcms.tool.description.component
 */
class PublisherComponent extends Manager
{

    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_ID,
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION,
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_IN_WORKSPACES,
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_WORKSPACE_ID);
    }
}
