<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LoadSavedEntryStatusesComponent extends Manager
{
    function run()
    {
        try
        {
            $presence = $this->getPresence();

            if (!$presence instanceof Presence)
            {
                $this->throwUserException('PresenceNotFound');
            }

            $contextIdentifier = $this->getPresenceServiceBridge()->getContextIdentifier();

            $statuses = $this->getPresenceService()->getDistinctSavedStatuses($contextIdentifier);

            $resultData = ['statuses' => $statuses];

            return new JsonResponse($this->serialize($resultData), 200, [], true);
        }
        catch (\Exception $ex)
        {
            return new JsonResponse(['error' => ['code' => 500, 'message' => $ex->getMessage()]], 500);
        }
    }
}
