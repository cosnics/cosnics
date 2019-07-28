<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Manager;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Teams\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class VisitPlatformGroupTeamComponent extends Manager
{
    /**
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function run()
    {
        $platformGroupTeam = $this->getPlatformGroupTeamFromRequest();

        try
        {
            $visitTeamUrl = $this->getPlatformGroupTeamService()->getVisitTeamUrl($this->getUser(), $platformGroupTeam);

            return new RedirectResponse($visitTeamUrl);
        }
        catch (UserException $ex)
        {
            throw $ex;
        }
        catch (\Exception $ex)
        {
            $message = 'TeamCanNotBeVisited';
            $this->getExceptionLogger()->logException($ex);
        }

        throw new UserException($this->getTranslator()->trans($message, [], Manager::context()));

    }

    public function get_additional_parameters()
    {
        return [self::PARAM_PLATFORM_GROUP_TEAM_ID];
    }

}