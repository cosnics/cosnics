<?php
namespace Chamilo\Application\Weblcms\Request;

use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'request_action';
    const PARAM_REQUEST_ID = 'request_id';
    const PARAM_RESET_CACHE = 'reset_cache';
    const ACTION_BROWSE = 'browser';
    const ACTION_CREATE = 'creator';
    const ACTION_DELETE = 'deleter';
    const ACTION_DENY = 'denier';
    const ACTION_GRANT = 'granter';
    const ACTION_RIGHTS = 'rights';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    public function request_allowed()
    {
        if ($this->get_user()->is_platform_admin())
        {
            return true;
        }

        $course_types = \Chamilo\Application\Weblcms\CourseType\Storage\DataManager :: retrieve_active_course_types();
        while ($course_type = $course_types->next_result())
        {
            if (CourseManagementRights :: get_instance()->is_allowed(
                CourseManagementRights :: REQUEST_COURSE_RIGHT,
                $course_type->get_id(),
                CourseManagementRights :: TYPE_COURSE_TYPE))
            {
                return true;
            }
        }

        return false;
    }
}
?>