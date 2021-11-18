<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LoadStatisticsComponent extends Manager
{
    function run()
    {
        try
        {
            $presence = $this->getPresence();
            if (!$this->canUserEditPresence())
            {
                throw new NotAllowedException();
            }
            $contextIdentifier = $this->getPresenceServiceBridge()->getContextIdentifier();
            $userIds = $this->getPresenceServiceBridge()->getTargetUserIds();
            $statistics = $this->getPresenceResultEntryService()->getResultPeriodStats($presence, $userIds, $contextIdentifier);
            return new JsonResponse($this->serialize(['statistics' => $statistics]), 200, [], true);
        }
        catch (\Exception $ex)
        {
            return new JsonResponse(['error' => ['code' => 500, 'message' => $ex->getMessage()]], 500);
        }
    }
}