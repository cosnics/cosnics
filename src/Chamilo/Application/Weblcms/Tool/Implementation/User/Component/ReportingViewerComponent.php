<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: user_details.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.user.component
 */
class ReportingViewerComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        BreadcrumbTrail :: get_instance()->add(
            new Breadcrumb($this->get_url(), Translation :: get('ReportingViewerComponent')));

        $factory = new ApplicationFactory(
            \Chamilo\Core\Reporting\Viewer\Manager :: context(),
           new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        $component = $factory->getComponent();
        $component->set_template_by_name(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\CourseStudentTrackerDetailTemplate :: class_name());
        return $component->run();
    }

    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID,
            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_COMPLEX_ID,
            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_TEMPLATE_NAME);
    }

    public function render_header($visible_tools)
    {
        return parent :: render_header($visible_tools, false);
    }
}
