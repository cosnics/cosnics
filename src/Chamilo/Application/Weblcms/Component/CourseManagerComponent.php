<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Course\Interfaces\CourseSubManagerSupport;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

/**
 * This class represents a component that runs the course type submanager
 *
 * @package \application\weblcms\course
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseManagerComponent extends Manager implements DelegateComponent, CourseSubManagerSupport
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManagePersonalCourses');

        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Application\Weblcms\Course\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
    }

    /**
     * Redirects the submanager to another component after a quick create
     *
     * @param $succes boolean
     * @param $message String
     */
    public function redirect_after_quick_create($succes, $message)
    {
        $this->redirect(
            $message,
            ! $succes,
            [],
            array(self::PARAM_ACTION, \Chamilo\Application\Weblcms\Course\Manager::PARAM_ACTION));
    }

    /**
     * Redirects the submanager to another component after a quick update
     *
     * @param boolean $succes
     * @param String $message
     */
    public function redirect_after_quick_update($succes, $message)
    {
        $this->redirect_after_quick_create($succes, $message);
    }
}
