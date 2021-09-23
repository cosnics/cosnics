<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

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

            $registeredPresenceEntryStatuses = $this->getPresenceService()->getRegisteredPresenceEntryStatuses($presence->getId());
            $savedStatuses = $this->getCurrentStatuses($presence);

            $this->getPresenceValidationService()->validateStatuses($data['statuses'], $savedStatuses, $registeredPresenceEntryStatuses);

            $presence->setOptions($this->serialize($data['statuses']));
            $presence->update();

            return new JsonResponse($this->serialize(['message' => 'ok']), 200, [], true);
        }
        catch (\Exception $ex)
        {
            return new JsonResponse(['error' => ['code' => 500, 'message' => $ex->getMessage()]], 500);
        }
    }

    /**
     * @param Presence $presence
     * @return array
     */
    protected function getCurrentStatuses(Presence $presence): array
    {
        $currentStatuses = [];
        $statuses = $this->deserialize($presence->getOptions());
        foreach ($statuses as $status)
        {
            $id = $status['id'];
            $currentStatuses[$id] = $status;
        }
        return $currentStatuses;
    }

}
