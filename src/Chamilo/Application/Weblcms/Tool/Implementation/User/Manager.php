<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseGroupRelation;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * $Id: user_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.user.component
 */

/**
 * This tool allows a user to publish users in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
{
    const ACTION_SUBSCRIBE_USER_BROWSER = 'SubscribeBrowser';
    const ACTION_SUBSCRIBE_GROUP_BROWSER = 'GroupSubscribeBrowser';
    const ACTION_UNSUBSCRIBE_BROWSER = 'UnsubscribeBrowser';
    const ACTION_UNSUBSCRIBE = 'Unsubscribe';
    const ACTION_SUBSCRIBE = 'Subscribe';
    const ACTION_SUBSCRIBE_AS_ADMIN = 'SubscribeAsAdmin';
    const ACTION_SUBSCRIBE_GROUPS = 'GroupSubscribe';
    const ACTION_SUBSCRIBE_USERS_FROM_GROUP = 'GroupUsersSubscribe';
    const ACTION_UNSUBSCRIBE_GROUPS = 'GroupUnsubscribe';
    const ACTION_REQUEST_SUBSCRIBE_USER = 'RequestSubscribeUser';
    const ACTION_REQUEST_SUBSCRIBE_USERS = 'RequestSubscribeUsers';
    const ACTION_USER_DETAILS = 'Details';
    const ACTION_EMAIL = 'Emailer';
    const ACTION_REPORTING = 'ReportingViewer';
    const ACTION_CHANGE_USER_STATUS_STUDENT = 'StatusChangerUserStudent';
    const ACTION_CHANGE_USER_STATUS_TEACHER = 'StatusChangerUserTeacher';
    const ACTION_CHANGE_PLATFORMGROUP_STATUS_STUDENT = 'StatusChangerPlatformgroupStudent';
    const ACTION_CHANGE_PLATFORMGROUP_STATUS_TEACHER = 'StatusChangerPlatformgroupTeacher';
    const ACTION_VIEW_AS = 'ViewAs';
    const ACTION_EXPORT = 'Exporter';
    const DEFAULT_ACTION = self :: ACTION_UNSUBSCRIBE_BROWSER;
    const PARAM_REFERER = 'referer';
    const PARAM_OBJECTS = 'objects';
    const PARAM_STATUS = 'status';
    const PARAM_TAB = 'tab';

    public function get_status_changer_url($user, $status)
    {
        // return $this->get_url(array(self :: PARAM_ACTION => self ::
        // ACTION_CHANGE_STATUS, self :: PARAM_OBJECTS => $user, self ::
        // PARAM_STATUS => $status, self :: PARAM_TAB => Request :: get(self ::
        // PARAM_TAB)));
        switch ($status)
        {
            case 1 :
                $url = $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_CHANGE_USER_STATUS_TEACHER,
                        self :: PARAM_OBJECTS => $user,
                        self :: PARAM_TAB => Request :: get(self :: PARAM_TAB)));
                break;
            case 5 :
                $url = $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_CHANGE_USER_STATUS_STUDENT,
                        self :: PARAM_OBJECTS => $user,
                        self :: PARAM_TAB => Request :: get(self :: PARAM_TAB)));
                break;
        }
        return $url;
    }

    public function get_platformgroup_status_changer_url($group, $status)
    {
        // return $this->get_url(array(self :: PARAM_ACTION => self ::
        // ACTION_CHANGE_PLATFORMGROUP_STATUS, self :: PARAM_OBJECTS => $group,
        // self :: PARAM_STATUS => $status, self :: PARAM_TAB => Request ::
        // get(self :: PARAM_TAB)));
        switch ($status)
        {
            case 1 :
                $url = $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_CHANGE_PLATFORMGROUP_STATUS_TEACHER,
                        self :: PARAM_OBJECTS => $group,
                        self :: PARAM_TAB => Request :: get(self :: PARAM_TAB)));
                break;
            case 5 :
                $url = $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_CHANGE_PLATFORMGROUP_STATUS_STUDENT,
                        self :: PARAM_OBJECTS => $group,
                        self :: PARAM_TAB => Request :: get(self :: PARAM_TAB)));
                break;
        }
        return $url;
    }

    public function get_subscribed_platformgroup_ids($course_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseGroupRelation :: class_name(), CourseGroupRelation :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id));

        $relations = $course_group_relations = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieves(
            CourseGroupRelation :: class_name(),
            $condition);

        $group_ids = array();
        while ($group = $relations->next_result())
        {
            $group_ids[] = $group->get_group_id();
        }
        return $group_ids;
    }
}
