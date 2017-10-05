<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Session\Request;

/**
 * $Id: reporting.class.php 218 2009-11-13 14:21:26Z kariboe $
 *
 * @package application.lib.weblcms.weblcms_manager.component
 */
class ReportingComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageCourses');

        $template_id = Request::get(self::PARAM_TEMPLATE_ID);

        if (! isset($template_id))
        {
            $this->set_parameter(
                self::PARAM_TEMPLATE_ID,
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\CourseDataTemplate::class_name());

            $application = $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Reporting\Viewer\Manager::context(),
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            $application->set_template_by_name(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\CourseDataTemplate::class_name());
            return $application->run();
        }
        else
        {
            $this->set_parameter(self::PARAM_TEMPLATE_ID, $template_id);

            $application = $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Reporting\Viewer\Manager::context(),
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            $application->set_template_by_name($template_id);
            return $application->run();
        }
    }
}