<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Exception;

/**
 * $Id: reporting_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.tool.reporting.component
 */

/**
 *
 * @author Michael Kyndt
 */
class ViewerComponent extends Manager
{

    public function run()
    {
        if (! $this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new Exception('not-allowed');
        }
        
        $template_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID);
        
        if (! isset($template_id))
        {
            $factory = new ApplicationFactory(
                \Chamilo\Core\Reporting\Viewer\Manager::context(), 
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            $component = $factory->getComponent();
            $component->set_template_by_name(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\CourseStudentTrackerTemplate::class_name());
            return $component->run();
        }
        else
        {
            if ($view = Request::get(\Chamilo\Core\Reporting\Viewer\Manager::PARAM_VIEW))
            {
                $this->set_parameter(\Chamilo\Core\Reporting\Viewer\Manager::PARAM_VIEW, $view);
            }
            
            $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID, $template_id);
            
            $factory = new ApplicationFactory(
                \Chamilo\Core\Reporting\Viewer\Manager::context(), 
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            $component = $factory->getComponent();
            $component->set_template_by_name($template_id);
            return $component->run();
        }
    }

    function get_additional_parameters()
    {
        return array(
            \Chamilo\Application\Weblcms\Manager::PARAM_USERS, 
            \Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID, 
            \Chamilo\Core\Reporting\Viewer\Manager::PARAM_BLOCK_ID, 
            \Chamilo\Application\Weblcms\Manager::PARAM_COURSE);
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    }
}
