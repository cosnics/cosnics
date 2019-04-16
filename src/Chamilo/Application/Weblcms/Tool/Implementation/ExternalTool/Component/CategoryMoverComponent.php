<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Manager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

class CategoryMoverComponent extends Manager
{

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }
}