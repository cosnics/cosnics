<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\CourseStudentTrackerDetailTemplate;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.lib.weblcms.tool.user.component
 */
class ReportingViewerComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        BreadcrumbTrail::getInstance()->add(
            new Breadcrumb($this->get_url(), Translation::get('ReportingViewerComponent')));

        $component = $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Reporting\Viewer\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        $component->set_template_by_name(
            CourseStudentTrackerDetailTemplate::class_name());
        return $component->run();
    }

    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID,
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_COMPLEX_ID,
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_TEMPLATE_NAME,
            \Chamilo\Application\Weblcms\Manager::PARAM_COURSE);
    }

    public function render_header($visible_tools)
    {
        return parent::render_header($visible_tools, false);
    }
}
