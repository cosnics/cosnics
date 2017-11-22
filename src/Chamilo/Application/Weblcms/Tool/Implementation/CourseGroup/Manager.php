<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package application.lib.weblcms.tool.course_group
 */
/**
 * This tool allows a course_group to publish course_groups in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements IntroductionTextSupportInterface
{
    const TOOL_NAME = 'course_group';
    const PARAM_COURSE_GROUP_ACTION = 'tool_action';
    const PARAM_DELETE_COURSE_GROUPS = 'delete_course_groups';
    const PARAM_UNSUBSCRIBE_USERS = 'unsubscribe_users';
    const PARAM_COURSE_GROUP = 'course_group';
    const PARAM_TAB = 'tab';
    const ACTION_SUBSCRIBE = 'SubscribeBrowser';
    const ACTION_UNSUBSCRIBE = 'UnsubscribeBrowser';
    const ACTION_ADD_COURSE_GROUP = 'Creator';
    const ACTION_EDIT_COURSE_GROUP = 'Editor';
    const ACTION_DELETE_COURSE_GROUP = 'Deleter';
    const ACTION_USER_SELF_SUBSCRIBE = 'SelfSubscriber';
    const ACTION_USER_SELF_UNSUBSCRIBE = 'SelfUnsubscriber';
    const ACTION_VIEW_GROUPS = 'Browser';
    const ACTION_MANAGE_SUBSCRIPTIONS = 'ManageSubscriptions';
    const ACTION_SUBSCRIPTIONS_OVERVIEW = 'SubscriptionsOverviewer';
    const ACTION_EXPORT_SUBSCRIPTIONS_OVERVIEW = 'Exporter';
    const ACTION_GROUP_DETAILS = 'Details';

    public function get_course_group()
    {
        $course_group_id = Request::get(self::PARAM_COURSE_GROUP);

        return \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            CourseGroup::class_name(),
            $course_group_id);
    }

    public function get_additional_parameters()
    {
        return array(self::PARAM_COURSE_GROUP);
    }
}
