<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;

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

            $statusDefaults = $this->getTranslator()->getLocale() == 'nl' ? Presence::STATUS_DEFAULTS_NL : Presence::STATUS_DEFAULTS_EN;

            $resultData = [
                'status-defaults' => json_decode($statusDefaults),
                'presence' => [
                    'id' => $presence->getId(),
                    'title' => $presence->get_title(),
                    'statuses' => json_decode($presence->getOptions())
                ]
            ];

            http_response_code(200);
            header('Content-type: application/json');
            echo json_encode($resultData);
        }
        catch (\Exception $ex)
        {
            http_response_code(500);
            header('Content-type: application/json');
            echo $ex->getMessage();
        }
    }
}
