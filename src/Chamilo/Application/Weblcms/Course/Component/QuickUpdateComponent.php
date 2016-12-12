<?php
namespace Chamilo\Application\Weblcms\Course\Component;

use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * This class describes an action to update a course quickly without going through the browser.
 * This class redirects you
 * back out of the submanager
 * 
 * @package \application\weblcms\course
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class QuickUpdateComponent extends UpdateComponent implements DelegateComponent
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    public function run()
    {
        $this->checkAuthorization(\Chamilo\Application\Weblcms\Manager::context(), 'ManageCourses');
        parent::run();
    }

    /**
     * Redirects this component after the update
     * 
     * @param boolean $succes
     * @param String $message
     */
    protected function redirect_after_form_handling($succes, $message)
    {
        $this->get_parent()->redirect_after_quick_update($succes, $message);
    }

    /**
     * Breadcrumbs are built semi automatically with the given application, subapplication, component...
     * Use this
     * function to add other breadcrumbs between the application / subapplication and the current component
     * 
     * @param \libraries\format\BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_course_create');
    }
}
