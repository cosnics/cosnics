<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class UpdatePresenceGlobalSelfRegistrationComponent extends Manager implements CsrfComponentInterface
{
    function run()
    {
        try
        {
            if (!$this->canUserEditPresence())
            {
                throw new NotAllowedException();
            }
            $this->ajaxComponent->validateIsPostRequest();

            $presence = $this->getPresence();

            $json = $this->getRequest()->getFromPost('data');
            $data = $this->deserialize($json);

            if ($data['id'] != $presence->getId())
            {
                $this->throwUserException('InvalidPresenceId');
            }

            $this->getPresenceService()->setPresenceSelfRegistrationDisabled($presence, $data['self_registration_disabled']);

            return new JsonResponse($this->serialize(['status' => 'ok']), 200, [], true);
        }
        catch (\Exception $ex)
        {
            return new JsonResponse(['error' => ['code' => 500, 'message' => $ex->getMessage()]], 500);
        }
    }
}
