<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup;

use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupDecoratorsManager;
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
    const ACTION_ADD_COURSE_GROUP = 'Creator';

    const ACTION_DELETE_COURSE_GROUP = 'Deleter';

    const ACTION_EDIT_COURSE_GROUP = 'Editor';

    const ACTION_EXPORT_SUBSCRIPTIONS_OVERVIEW = 'Exporter';

    const ACTION_GROUP_DETAILS = 'Details';

    const ACTION_LAUNCH_INTEGRATION = 'IntegrationLauncher';

    const ACTION_MANAGE_SUBSCRIPTIONS = 'ManageSubscriptions';

    const ACTION_SUBSCRIBE = 'SubscribeBrowser';

    const ACTION_SUBSCRIPTIONS_OVERVIEW = 'SubscriptionsOverviewer';

    const ACTION_UNSUBSCRIBE = 'UnsubscribeBrowser';

    const ACTION_USER_SELF_SUBSCRIBE = 'SelfSubscriber';

    const ACTION_USER_SELF_UNSUBSCRIBE = 'SelfUnsubscriber';

    const ACTION_VIEW_GROUPS = 'Browser';

    const PARAM_COURSE_GROUP = 'course_group';

    const PARAM_COURSE_GROUP_ACTION = 'tool_action';

    const PARAM_DELETE_COURSE_GROUPS = 'delete_course_groups';

    const PARAM_TAB = 'tab';

    const PARAM_UNSUBSCRIBE_USERS = 'unsubscribe_users';

    const TOOL_NAME = 'course_group';

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupDecoratorsManager
     */
    protected function getCourseGroupDecoratorsManager()
    {
        return $this->getService(CourseGroupDecoratorsManager::class);
    }

    public function get_additional_parameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_COURSE_GROUP;

        return $additionalParameters;
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
