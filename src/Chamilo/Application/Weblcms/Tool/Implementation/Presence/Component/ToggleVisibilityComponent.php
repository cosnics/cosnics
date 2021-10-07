<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Presence\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Presence\Manager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

class ToggleVisibilityComponent extends Manager
{

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }
}
