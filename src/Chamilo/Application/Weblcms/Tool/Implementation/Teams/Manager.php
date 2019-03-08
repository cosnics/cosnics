<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams;

/**
 * @inheritdoc
 */
class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
{
    const DEFAULT_ACTION = self::ACTION_BROWSE;
    const ACTION_BROWSE = 'Browser';
    const ACTION_CREATE_TEAM = 'CreateTeam';
    const ACTION_GO_TO_TEAM = 'GoToTeam';
    const ACTION_REMOVE_TEAM_USERS_NOT_IN_COURSE = 'RemoveTeamUsersNotInCourse';
    const ACTION_SUBSCRIBE_ALL_COURSE_USERS_TO_TEAM = 'SubscribeAllCourseUsersToTeam';
}