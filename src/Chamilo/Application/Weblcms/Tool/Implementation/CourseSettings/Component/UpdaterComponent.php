<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseSettings\Component;

use Chamilo\Application\Weblcms\Course\Interfaces\CourseSubManagerSupport;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseSettings\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;

/**
 * $Id: course_settings_updater.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.course_settings.component
 */
class UpdaterComponent extends Manager implements CourseSubManagerSupport
{

    public function run()
    {
        if (! $this->get_course()->is_course_admin($this->get_user()))
        {
            throw new NotAllowedException();
        }

        Request :: set_get(
            \Chamilo\Application\Weblcms\Course\Manager :: PARAM_ACTION,
            \Chamilo\Application\Weblcms\Course\Manager :: ACTION_QUICK_UPDATE);
        Request :: set_get(\Chamilo\Application\Weblcms\Course\Manager :: PARAM_COURSE_ID, $this->get_course_id());

        $this->getRequest()->query->set(
            \Chamilo\Application\Weblcms\Course\Manager :: PARAM_ACTION,
            \Chamilo\Application\Weblcms\Course\Manager :: ACTION_QUICK_UPDATE);
        $this->getRequest()->query->set(
            \Chamilo\Application\Weblcms\Course\Manager :: PARAM_COURSE_ID,
            $this->get_course_id());

        $factory = new ApplicationFactory(
            \Chamilo\Application\Weblcms\Course\Manager :: context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }

    public function redirect_after_quick_create($succes, $message)
    {
        $this->redirect($message, ! $succes, array(), array(self :: PARAM_ACTION, self :: ACTION_UPDATE));
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

    /**
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {

    }
}
