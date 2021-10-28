<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Presence\Domain\PresenceResultEntryFilterOptions;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class BulkSavePresenceEntriesComponent extends Manager implements CsrfComponentInterface
{
    function run()
    {
        try
        {
            if (!$this->canUserEditPresence())
            {
                throw new NotAllowedException();
            }
            $this->validatePresenceResultEntryInput();

            $contextIdentifier = $this->getPresenceServiceBridge()->getContextIdentifier();

            $periodId = $this->getRequest()->getFromPostOrUrl('period_id');
            $statusId = $this->getRequest()->getFromPostOrUrl('status_id');

            $targetUserIds = $this->getPresenceServiceBridge()->getTargetUserIds();

            $filterOptions = new PresenceResultEntryFilterOptions();
            $filterOptions->periodId = $periodId;
            $filterOptions->withoutStatus = true;

            $users = $this->getUserService()->getUsersFromIds($targetUserIds, $contextIdentifier, new FilterParameters(), $filterOptions);

            $this->getPresenceResultEntryService()->createOrUpdatePresenceResultEntries($this->getPresence(), $periodId, $users, $statusId);

            return new JsonResponse($this->serialize(['status' => 'ok']), 200, [], true);
        }
        catch (\Exception $ex)
        {
            return new JsonResponse(['error' => ['code' => 500, 'message' => $ex->getMessage()]], 500);
        }
    }

}