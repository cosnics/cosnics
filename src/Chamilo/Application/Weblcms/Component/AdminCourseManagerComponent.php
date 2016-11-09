<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
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

    /**
     * Returns the admin breadcrumb generator
     * 
     * @return \libraries\format\BreadcrumbGeneratorInterface
     */
    public function get_breadcrumb_generator()
    {
        return new \Chamilo\Core\Admin\Core\BreadcrumbGenerator($this, BreadcrumbTrail :: getInstance());
    }
}
