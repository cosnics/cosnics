<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\CourseDataTemplate;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Session\Request;

/**
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
                CourseDataTemplate::class);

            $application = $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Reporting\Viewer\Manager::context(),
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            $application->set_template_by_name(
                CourseDataTemplate::class);
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