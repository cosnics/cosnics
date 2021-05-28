<?php
namespace Chamilo\Application\Weblcms\Request;

use Chamilo\Application\Weblcms\CourseType\Storage\DataManager;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'request_action';
    const PARAM_REQUEST_ID = 'request_id';
    const PARAM_RESET_CACHE = 'reset_cache';
    const ACTION_BROWSE = 'Browser';
    const ACTION_CREATE = 'Creator';
    const ACTION_DELETE = 'Deleter';
    const ACTION_DENY = 'Denier';
    const ACTION_GRANT = 'Granter';
    const ACTION_RIGHTS = 'Rights';
    const DEFAULT_ACTION = self::ACTION_BROWSE;

    public function request_allowed()
    {
        if ($this->get_user()->is_platform_admin())
        {
            return true;
        }
        
        $course_types = DataManager::retrieve_active_course_types();
        foreach($course_types as $course_type)
        {
            if (CourseManagementRights::getInstance()->is_allowed_management(
                CourseManagementRights::REQUEST_COURSE_RIGHT, 
                $course_type->get_id(), 
                CourseManagementRights::TYPE_COURSE_TYPE))
            {
                return true;
            }
        }
        
        return false;
    }
}
?>