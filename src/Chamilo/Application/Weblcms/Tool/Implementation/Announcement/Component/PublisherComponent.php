<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Announcement\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Announcement\Manager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

class PublisherComponent extends Manager
{

    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_ID,
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION,
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_IN_WORKSPACES,
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_WORKSPACE_ID,
        );
    }

    /**
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }
}
