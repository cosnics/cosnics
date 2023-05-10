<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup;

use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupDecoratorsManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Libraries\Platform\Session\Request;

/**
 * @package application.lib.weblcms.tool.course_group
 */

/**
 * This tool allows a course_group to publish course_groups in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements IntroductionTextSupportInterface
{
    public const ACTION_ADD_COURSE_GROUP = 'Creator';
    public const ACTION_DELETE_COURSE_GROUP = 'Deleter';
    public const ACTION_EDIT_COURSE_GROUP = 'Editor';
    public const ACTION_EXPORT_SUBSCRIPTIONS_OVERVIEW = 'Exporter';
    public const ACTION_GROUP_DETAILS = 'Details';
    public const ACTION_LAUNCH_INTEGRATION = 'IntegrationLauncher';
    public const ACTION_MANAGE_SUBSCRIPTIONS = 'ManageSubscriptions';
    public const ACTION_SUBSCRIBE = 'SubscribeBrowser';
    public const ACTION_SUBSCRIPTIONS_OVERVIEW = 'SubscriptionsOverviewer';
    public const ACTION_UNSUBSCRIBE = 'UnsubscribeBrowser';
    public const ACTION_USER_SELF_SUBSCRIBE = 'SelfSubscriber';
    public const ACTION_USER_SELF_UNSUBSCRIBE = 'SelfUnsubscriber';
    public const ACTION_VIEW_GROUPS = 'Browser';

    public const CONTEXT = __NAMESPACE__;

    public const PARAM_COURSE_GROUP = 'course_group';
    public const PARAM_COURSE_GROUP_ACTION = 'tool_action';
    public const PARAM_DELETE_COURSE_GROUPS = 'delete_course_groups';
    public const PARAM_TAB = 'tab';
    public const PARAM_UNSUBSCRIBE_USERS = 'unsubscribe_users';

    public const TOOL_NAME = 'course_group';

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_COURSE_GROUP;

        return parent::getAdditionalParameters($additionalParameters);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupDecoratorsManager
     */
    protected function getCourseGroupDecoratorsManager()
    {
        return $this->getService(CourseGroupDecoratorsManager::class);
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass | CourseGroup
     */
    public function get_course_group()
    {
        $course_group_id = Request::get(self::PARAM_COURSE_GROUP);

        return DataManager::retrieve_by_id(
            CourseGroup::class, $course_group_id
        );
    }
}
