<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Teams\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RemoveTeamUsersNotInGroupsComponent extends Manager
{
    /**
     * @return string|void
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function run()
    {
        if (!$this->get_course()->is_course_admin($this->getUser()))
        {
            throw new NotAllowedException();
        }

        $platformGroupTeam = $this->getPlatformGroupTeamFromRequest();

        try
        {
            $this->getPlatformGroupTeamService()->removeTeamUsersNotInGroups($this->get_course(), $platformGroupTeam);

            $message = 'TeamUsersNotInGroupsRemoved';
            $success = true;
        }
        catch (UserException $ex)
        {
            throw $ex;
        }
        catch (\Exception $ex)
        {
            $message = 'TeamUsersNotInGroupsNotRemoved';
            $success = false;
            $this->getExceptionLogger()->logException($ex);
        }

        $this->redirect(
            $this->getTranslator()->trans($message, [], Manager::context()), !$success,
            [self::PARAM_ACTION => self::ACTION_BROWSE], [self::PARAM_PLATFORM_GROUP_TEAM_ID]
        );
    }

    public function get_additional_parameters()
    {
        return [self::PARAM_PLATFORM_GROUP_TEAM_ID];
    }
}