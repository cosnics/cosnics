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
class UpdatePresenceComponent extends Manager
{
    function run()
    {
        try
        {
            $this->ajaxComponent->validateIsPostRequest();
            $presence = $this->getPresence();

            if (!$presence instanceof Presence)
            {
                $this->throwUserException('PresenceNotFound');
            }

            $json = $this->getRequest()->getFromPost('data');
            $data = $this->deserialize($json);

            return new JsonResponse($this->serialize(['message' => 'ok']), 200, [], true);
        }
        catch (\Exception $ex)
        {
            return new JsonResponse(['error' => ['code' => 500, 'message' => $ex->getMessage()]], 500);
        }
    }
}
