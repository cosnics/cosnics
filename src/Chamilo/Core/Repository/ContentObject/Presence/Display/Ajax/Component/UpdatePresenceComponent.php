<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Presence\Domain\Exceptions\PresenceValidationException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class UpdatePresenceComponent extends Manager implements CsrfComponentInterface
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

            $this->getPresenceValidationService()->validateStatuses($presence, $data['statuses']);
            $verifyIcon = empty($data['verification_icon_data']) ? array() : $data['verification_icon_data'];
            $this->getPresenceService()->setPresenceOptions($presence, $data['statuses'], $verifyIcon, $data['has_checkout']);

            return new JsonResponse($this->serialize(['status' => 'ok']), 200, [], true);
        }
        catch (PresenceValidationException $ex)
        {
            $err = ['type' => $ex->getErrorCode(), 'status_id' => $ex->getPresenceStatusId()];
            if (!empty($ex->getSavedStatus()))
            {
                $err['status'] = $ex->getSavedStatus();
             }
            return new JsonResponse(['error' => $err], Response::HTTP_CONFLICT);
        }
        catch (\Exception $ex)
        {
            return new JsonResponse(['error' => ['code' => 500, 'message' => $ex->getMessage()]], 500);
        }
    }
}
