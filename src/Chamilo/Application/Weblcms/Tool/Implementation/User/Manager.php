<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User;

use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package application.lib.weblcms.tool.user.component
 */

/**
 * This tool allows a user to publish users in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
{
    public const ACTION_CHANGE_PLATFORMGROUP_STATUS_STUDENT = 'StatusChangerPlatformgroupStudent';
    public const ACTION_CHANGE_PLATFORMGROUP_STATUS_TEACHER = 'StatusChangerPlatformgroupTeacher';
    public const ACTION_CHANGE_USER_STATUS_STUDENT = 'StatusChangerUserStudent';
    public const ACTION_CHANGE_USER_STATUS_TEACHER = 'StatusChangerUserTeacher';
    public const ACTION_EMAIL = 'Emailer';
    public const ACTION_EXPORT = 'Exporter';
    public const ACTION_REPORTING = 'ReportingViewer';
    public const ACTION_REQUEST_SUBSCRIBE_USER = 'RequestSubscribeUser';
    public const ACTION_REQUEST_SUBSCRIBE_USERS = 'RequestSubscribeUsers';
    public const ACTION_SUBSCRIBE = 'Subscribe';
    public const ACTION_SUBSCRIBE_AS_ADMIN = 'SubscribeAsAdmin';
    public const ACTION_SUBSCRIBE_GROUPS = 'GroupSubscribe';
    public const ACTION_SUBSCRIBE_GROUPS_SEARCHER = 'SubscribeGroupsSearcher';
    public const ACTION_SUBSCRIBE_GROUP_DETAILS = 'SubscribeGroupsDetails';
    public const ACTION_SUBSCRIBE_GROUP_SUBGROUP_BROWSER = 'SubscribeGroupsBrowseSubgroups';
    public const ACTION_SUBSCRIBE_USERS_FROM_GROUP = 'GroupUsersSubscribe';
    public const ACTION_SUBSCRIBE_USER_BROWSER = 'SubscribeBrowser';
    public const ACTION_UNSUBSCRIBE = 'Unsubscribe';
    public const ACTION_UNSUBSCRIBE_BROWSER = 'UnsubscribeBrowser';
    public const ACTION_UNSUBSCRIBE_GROUPS = 'GroupUnsubscribe';
    public const ACTION_USER_DETAILS = 'Details';
    public const ACTION_VIEW_AS = 'ViewAs';

    public const CONTEXT = __NAMESPACE__;

    public const DEFAULT_ACTION = self::ACTION_UNSUBSCRIBE_BROWSER;

    public const PARAM_GROUP = 'group';
    public const PARAM_OBJECTS = 'objects';
    public const PARAM_REFERER = 'referer';
    public const PARAM_STATUS = 'status';
    public const PARAM_TAB = 'tab';

    /**
     * Adds a breadcrumb to the browser component
     *
     * @param BreadcrumbTrail $breadcrumbTrail
     */
    protected function addBrowserBreadcrumb(BreadcrumbTrail $breadcrumbTrail)
    {
        $breadcrumbTrail->add(
            new Breadcrumb(
                $this->get_url([self::PARAM_ACTION => self::ACTION_UNSUBSCRIBE_BROWSER]),
                Translation::getInstance()->getTranslation('UnsubscribeBrowserComponent', [], __NAMESPACE__)
            )
        );
    }

    public function get_platformgroup_status_changer_url($group, $status)
    {
        switch ($status)
        {
            case 1 :
                $url = $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_CHANGE_PLATFORMGROUP_STATUS_TEACHER,
                        self::PARAM_OBJECTS => $group,
                        self::PARAM_TAB => $this->getRequest()->query->get(self::PARAM_TAB)
                    ]
                );
                break;
            case 5 :
                $url = $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_CHANGE_PLATFORMGROUP_STATUS_STUDENT,
                        self::PARAM_OBJECTS => $group,
                        self::PARAM_TAB => $this->getRequest()->query->get(self::PARAM_TAB)
                    ]
                );
                break;
        }

        return $url;
    }

    public function get_status_changer_url($user, $status)
    {
        switch ($status)
        {
            case 1 :
                $url = $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_CHANGE_USER_STATUS_TEACHER,
                        self::PARAM_OBJECTS => $user,
                        self::PARAM_TAB => $this->getRequest()->query->get(self::PARAM_TAB)
                    ]
                );
                break;
            case 5 :
                $url = $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_CHANGE_USER_STATUS_STUDENT,
                        self::PARAM_OBJECTS => $user,
                        self::PARAM_TAB => $this->getRequest()->query->get(self::PARAM_TAB)
                    ]
                );
                break;
        }

        return $url;
    }

    public function get_subscribed_platformgroup_ids($course_id)
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_GROUP)
        );

        return DataManager::distinct(
            CourseEntityRelation::class, new DataClassDistinctParameters(
                new AndCondition($conditions), new RetrieveProperties(
                    [
                        new PropertyConditionVariable(
                            CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID
                        )
                    ]
                )
            )
        );
    }

    public function isGroupSubscribed($groupId): bool
    {
        $group = $this->getGroupService()->findGroupByIdentifier($groupId);
        $parents = $group->get_ancestors();

        $subscribedPlatformGroupIds = $this->get_subscribed_platformgroup_ids($this->get_course_id());

        foreach ($parents as $parent)
        {
            if (in_array($parent->getId(), $subscribedPlatformGroupIds))
            {
                return true;
            }
        }

        return false;
    }
}
