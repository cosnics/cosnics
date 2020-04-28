<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams;

use Chamilo\Application\Weblcms\Service\CourseSubscriptionService;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service\CourseTeamService;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service\PlatformGroupTeamService;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;

/**
 * @inheritdoc
 */
class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
{
    const DEFAULT_ACTION = self::ACTION_BROWSE;
    const ACTION_BROWSE = 'Browser';

    const ACTION_CREATE_TEAM = 'CreateTeam';
    const ACTION_DELETE_TEAM = 'DeleteCourseTeam';
    const ACTION_GO_TO_TEAM = 'GoToTeam';
    const ACTION_REMOVE_TEAM_USERS_NOT_IN_COURSE = 'RemoveTeamUsersNotInCourse';
    const ACTION_SUBSCRIBE_ALL_COURSE_USERS_TO_TEAM = 'SubscribeAllCourseUsersToTeam';

    const ACTION_CREATE_PLATFORM_GROUP_TEAM = 'CreatePlatformGroupTeam';
    const ACTION_EDIT_PLATFORM_GROUP_TEAM = 'EditPlatformGroupTeam';
    const ACTION_DELETE_PLATFORM_GROUP_TEAM = 'DeletePlatformGroupTeam';
    const ACTION_REMOVE_TEAM_USERS_NOT_IN_GROUPS = 'RemoveTeamUsersNotInGroups';
    const ACTION_SUBSCRIBE_PLATFORM_GROUP_TEAM_USERS = 'SubscribePlatformGroupTeamUsers';
    const ACTION_VISIT_PLATFORM_GROUP_TEAM = 'VisitPlatformGroupTeam';

    const ACTION_AJAX = 'Ajax';

    const PARAM_PLATFORM_GROUP_TEAM_ID = 'PlatformGroupTeamId';

    /**
     * @return PlatformGroupTeamService
     */
    protected function getPlatformGroupTeamService()
    {
        return $this->getService(PlatformGroupTeamService::class);
    }

    /**
     * @return CourseTeamService
     */
    protected function getCourseTeamService(): CourseTeamService
    {
        return $this->getService(CourseTeamService::class);
    }

    /**
     * @return PlatformGroupTeam
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     */
    protected function getPlatformGroupTeamFromRequest()
    {
        $platformGroupTeamId = $this->getRequest()->getFromUrl(self::PARAM_PLATFORM_GROUP_TEAM_ID);
        $platformGroupTeam = null;

        if (!empty($platformGroupTeamId))
        {
            $platformGroupTeam = $this->getPlatformGroupTeamService()->findPlatformGroupTeamById($platformGroupTeamId);
        }

        if (!$platformGroupTeam instanceof PlatformGroupTeam)
        {
            throw new NoObjectSelectedException(
                $this->getTranslator()->trans('PlatformGroupTeam', [], Manager::context())
            );
        }

        return $platformGroupTeam;
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

    /**
     * @param string $ajaxAction
     *
     * @param array $parameters
     *
     * @return string
     */
    public function getAjaxUrl(string $ajaxAction, $parameters = [])
    {
        $parameters[self::PARAM_ACTION] = self::ACTION_AJAX;
        $parameters[\Chamilo\Application\Weblcms\Tool\Implementation\Teams\Ajax\Manager::PARAM_ACTION] = $ajaxAction;

        return $this->get_url($parameters);
    }
}
