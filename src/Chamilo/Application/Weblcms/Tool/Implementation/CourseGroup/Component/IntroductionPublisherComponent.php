<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

class IntroductionPublisherComponent extends Manager
{

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_course_group_introduction_publisher');
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }
}
