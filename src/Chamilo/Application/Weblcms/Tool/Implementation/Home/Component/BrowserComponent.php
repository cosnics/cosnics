<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Home\Component;

use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

class BrowserComponent extends Manager
{

    public function run()
    {
        $courseTools = $this->get_visible_tools();
        
        $introductionAllowed = CourseSettingsController::getInstance()->get_course_setting(
            $this->get_course(), 
            \Chamilo\Application\Weblcms\CourseSettingsConnector::ALLOW_INTRODUCTION_TEXT);
        
        $type = 'Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer\Type\ListHomeRenderer';
        
        $homeRenderer = new $type($this, $courseTools, $introductionAllowed, $this->get_introduction_text());
        
        $html = array();
        
        $html[] = $this->render_header($courseTools, $introductionAllowed);
        $html[] = $homeRenderer->render();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_home_browser');
    }

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }
}
