<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Manager;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class CreatePresencePeriodComponent extends Manager implements CsrfComponentInterface
{
    function run()
    {
        try
        {
            $this->validatePresenceUserInput();
            $presence = $this->getPresence();
            $contextIdentifier = $this->getPresenceServiceBridge()->getContextIdentifier();

            $presenceResultPeriod = $this->getPresenceService()->createPresenceResultPeriod($presence, $contextIdentifier);

            $result = [
                'status' => 'ok',
                'id' => (int) $presenceResultPeriod->getId(),
                'label' => $presenceResultPeriod->getLabel()
            ];

            return new JsonResponse($this->serialize($result), 200, [], true);
        }
        catch (\Exception $ex) {
            return new JsonResponse(['error' => ['code' => 500, 'message' => $ex->getMessage()]], 500);
        }
    }
}