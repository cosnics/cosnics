<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Libraries\Platform\Session\Request;

/**
 * $Id: course_group_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.course_group
 */
/**
 * This tool allows a course_group to publish course_groups in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
{
    const TOOL_NAME = 'course_group';
    const PARAM_COURSE_GROUP_ACTION = 'tool_action';
    const PARAM_DELETE_COURSE_GROUPS = 'delete_course_groups';
    const PARAM_UNSUBSCRIBE_USERS = 'unsubscribe_users';
    const ACTION_SUBSCRIBE = 'subscribe_browser';
    const ACTION_UNSUBSCRIBE = 'unsubscribe_browser';
    const ACTION_ADD_COURSE_GROUP = 'creator';
    const ACTION_EDIT_COURSE_GROUP = 'editor';
    const ACTION_DELETE_COURSE_GROUP = 'deleter';
    const ACTION_USER_SELF_SUBSCRIBE = 'self_subscriber';
    const ACTION_USER_SELF_UNSUBSCRIBE = 'self_unsubscriber';
    const ACTION_VIEW_GROUPS = 'browser';
    const ACTION_MANAGE_SUBSCRIPTIONS = 'manage_subscriptions';
    const ACTION_SUBSCRIPTIONS_OVERVIEW = 'subscriptions_overviewer';
    const ACTION_EXPORT_SUBSCRIPTIONS_OVERVIEW = 'exporter';
    const PARAM_COURSE_GROUP = 'course_group';
    const PARAM_TAB = 'tab';

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Libraries\Architecture\Application\Application $parent
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request, $user = null, $parent = null)
    {
        parent :: __construct($request, $user, $parent);
        $this->set_parameter(self :: PARAM_COURSE_GROUP, Request :: get(self :: PARAM_COURSE_GROUP));
    }

    public function get_course_group()
    {
        $course_group_id = Request :: get(self :: PARAM_COURSE_GROUP);

        return \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            CourseGroup :: class_name(),
            $course_group_id);
    }
}
