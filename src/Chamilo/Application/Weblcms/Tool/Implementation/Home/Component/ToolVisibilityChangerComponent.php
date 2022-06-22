<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Home\Component;

use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager;
use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

class ToolVisibilityChangerComponent extends Manager
{
    // TODO: change this to new course tool structure
    public function run()
    {
        $tool = Request::get(self::PARAM_TOOL);
        $visibility = Request::get(self::PARAM_VISIBILITY);

        $toolRegistration = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_course_tool_by_name($tool);
        if(!$toolRegistration instanceof CourseTool)
        {
            throw new NotAllowedException();
        }

        $courseSettingsController = CourseSettingsController::getInstance();
        if(!$courseSettingsController->canChangeCourseSetting($this->get_course(), CourseSetting::COURSE_SETTING_TOOL_VISIBLE, $toolRegistration->getId()))
        {
            throw new NotAllowedException();
        }

        DataManager::set_tool_visibility_by_tool_name($this->get_course_id(), $tool, $visibility);
        $message = 'ToolVisibilityChanged';
        $this->redirectWithMessage(Translation::get($message), false, array(self::PARAM_ACTION => self::ACTION_BROWSE));
    }
}
