<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Core\Admin\Core\BreadcrumbGenerator;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\BreadcrumbGeneratorInterface;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * This class represents a component that runs the course submanager.
 * It's an extension from the normal launcher
 * component to support components runnable as administrator
 *
 * @package \application\weblcms\course
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class AdminCourseManagerComponent extends CourseManagerComponent implements DelegateComponent
{

    public function get_breadcrumb_generator(): BreadcrumbGeneratorInterface
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail::getInstance());
    }
}
