<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup;

use Chamilo\Application\Weblcms\Service\CourseSubscriptionService;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupService;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
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
    const ACTION_LAUNCH_INTEGRATION = 'IntegrationLauncher';

    /**
     * @return CourseGroup
     * @throws ObjectNotExistException
     */
    public function getCourseGroupFromRequest()
    {
        $courseGroupId = $this->getRequest()->getFromUrl(self::PARAM_COURSE_GROUP);

        $courseGroup = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            CourseGroup::class_name(),
            $courseGroupId
        );

        if (!$courseGroup instanceof CourseGroup)
        {
            throw new ObjectNotExistException(
                $this->getTranslator()->trans('CourseGroup', [], self::context()), $courseGroupId
            );
        }

        return $courseGroup;
    }

    public function get_additional_parameters()
    {
        return array(self::PARAM_COURSE_GROUP);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupDecoratorsManager
     */
    protected function getCourseGroupDecoratorsManager()
    {
        return $this->getService('chamilo.application.weblcms.tool.implementation.course_group.decorator.manager');
    }

    /**
     * @return array
     */
    protected function getDirectlySubscribedPlatformGroups()
    {
        return $this->getCourseSubscriptionService()->findGroupsDirectlySubscribedToCourse($this->get_course());
    }

    /**
     * @return \Chamilo\Application\Weblcms\Service\CourseSubscriptionService
     */
    protected function getCourseSubscriptionService()
    {
        return $this->getService(CourseSubscriptionService::class);
    }

    protected function addGroupDetailsBreadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    [self::PARAM_ACTION => self::ACTION_BROWSE],
                    [self::PARAM_COURSE_GROUP]
                ),
                $this->getTranslator()->trans('DetailsComponent', [], Manager::context())
            )
        );

        $currentGroup = $this->getCourseGroupFromRequest();
        $availableGroups = [];
        while(!$currentGroup->is_root())
        {
            array_unshift($availableGroups, $currentGroup);
            $currentGroup = $currentGroup->get_parent();
        }

        foreach($availableGroups as $currentGroup)
        {
            $breadcrumbtrail->add(
                new Breadcrumb(
                    $this->get_url(
                        [
                            self::PARAM_ACTION => self::ACTION_GROUP_DETAILS,
                            self::PARAM_COURSE_GROUP => $currentGroup->getId()
                        ]
                    ),
                    $currentGroup->get_name()
                )
            );
        }
    }

    /**
     * @return CourseGroupService
     */
    protected function getCourseGroupService()
    {
        return $this->getService(CourseGroupService::class);
    }
}
