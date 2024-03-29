<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\GradeBook\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\GradeBook\Manager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

class DeleterComponent extends Manager
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
