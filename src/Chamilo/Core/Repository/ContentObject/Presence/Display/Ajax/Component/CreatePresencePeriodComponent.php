<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Manager;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class CreatePresencePeriodComponent extends Manager
{
    function run()
    {
        try
        {
            $this->validatePresenceUserInput();
            $presence = $this->getPresence();
            $contextIdentifier = $this->getPresenceServiceBridge()->getContextIdentifier();
            $periodLabel = $this->getRequest()->getFromPostOrUrl('period_label') || '';

            $presenceResultPeriod = $this->getPresenceService()->createPresenceResultPeriod($presence, $contextIdentifier, $periodLabel);

            $result = [
                'status' => 'ok',
                'id' => $presenceResultPeriod->getId(),
                'label' => $presenceResultPeriod->getLabel()
            ];

            return new JsonResponse($this->serialize($result), 200, [], true);
        }
        catch (\Exception $ex) {
            return new JsonResponse(['error' => ['code' => 500, 'message' => $ex->getMessage()]], 500);
        }
    }
}