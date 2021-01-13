<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Ajax\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Ajax\Manager;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Teams\Ajax\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UpdateLocalTeamNameComponent extends Manager
{
    const PARAM_PLATFORM_GROUP_TEAM_IDS = 'PlatformGroupTeamIds';

    /**
     * @return string
     */
    function run()
    {
        try
        {
            $platformGroupTeamIds = json_decode($this->getRequest()->getFromPost(self::PARAM_PLATFORM_GROUP_TEAM_IDS));

            if (empty($platformGroupTeamIds))
            {
                return new JsonResponse([]);
            }

            $result = [];

            foreach ($platformGroupTeamIds as $platformGroupTeamId)
            {
                $result[$platformGroupTeamId] =
                    $this->getPlatformGroupTeamService()->updateLocalTeamNameById($platformGroupTeamId);
            }

            return new JsonResponse($result);
        }
        catch(\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex);
            return new JsonResponse([], 500);
        }
    }
}
