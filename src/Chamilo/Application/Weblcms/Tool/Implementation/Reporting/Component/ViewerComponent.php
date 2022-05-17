<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\CourseStudentTrackerTemplate;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package application.lib.weblcms.tool.reporting.component
 * @author Michael Kyndt
 */
class ViewerComponent extends Manager
{

    public function run()
    {
        if (! $this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $template_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID);

        $this->registerParameters();

        if (! isset($template_id))
        {
            $component = $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Reporting\Viewer\Manager::context(),
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            $component->set_template_by_name(
                CourseStudentTrackerTemplate::class);
            return $component->run();
        }
        else
        {
            if ($view = Request::get(\Chamilo\Core\Reporting\Viewer\Manager::PARAM_VIEW))
            {
                $this->set_parameter(\Chamilo\Core\Reporting\Viewer\Manager::PARAM_VIEW, $view);
            }

            $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID, $template_id);

            $component = $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Reporting\Viewer\Manager::context(),
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            $component->set_template_by_name($template_id);
            return $component->run();
        }
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    }

    protected function registerParameters()
    {
        $parameters = [
            \Chamilo\Application\Weblcms\Manager::PARAM_USERS,
            \Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID,
            \Chamilo\Core\Reporting\Viewer\Manager::PARAM_BLOCK_ID
        ];

        foreach($parameters as $parameter)
        {
            $this->set_parameter($parameter, $this->getRequest()->getFromUrl($parameter));
        }
    }
}
