<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Note\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Note\Manager;

/**
 *
 * @package application.lib.weblcms.tool.note.component
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
