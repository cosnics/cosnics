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
class LoadPresenceComponent extends Manager
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

            $statusDefaults = $this->getTranslator()->getLocale() == 'nl' ? Presence::FIXED_STATUS_DEFAULTS_NL : Presence::FIXED_STATUS_DEFAULTS_EN;

            $resultData = [
                'status-defaults' => $this->deserialize($statusDefaults),
                'presence' => [
                    'id' => (int) $presence->getId(),
                    'title' => $presence->get_title(),
                    'statuses' => $this->deserialize($presence->getOptions()),
                    'has_checkout' => $presence->hasCheckout()
                ]
            ];
            return new JsonResponse($this->serialize($resultData), 200, [], true);
        }
        catch (\Exception $ex)
        {
            return new JsonResponse(['error' => ['code' => 500, 'message' => $ex->getMessage()]], 500);
        }
    }
}
