<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\PresenceResultPeriod;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class SavePresenceEntryComponent extends Manager
{
    function run()
    {
        try
        {
            $this->validatePresenceUserInput();

            $periodId = $this->getRequest()->getFromPostOrUrl('period_id');
            $userId = $this->getRequest()->getFromPostOrUrl('user_id');
            $statusId = $this->getRequest()->getFromPostOrUrl('status_id');

            $presenceResultEntry = $this->getPresenceService()->createOrUpdatePresenceResultEntry($this->getPresence(), $periodId, $userId, $statusId);

            $result = [
                'period_id' => $presenceResultEntry->getPresencePeriodId(),
                'user_id' => $presenceResultEntry->getUserId(),
                'status_id' => $presenceResultEntry->getChoiceId(),
                'fixed_status_id' => $presenceResultEntry->getPresenceStatusId()
            ];

            return new JsonResponse($this->serialize($result), 200, [], true);
        }
        catch (\Exception $ex) {
            return new JsonResponse(['error' => ['code' => 500, 'message' => $ex->getMessage()]], 500);
        }
    }

    /**
     * @throws NotAllowedException
     * @throws UserException
     */
    protected function validatePresenceUserInput()
    {
        parent::validatePresenceUserInput();

        $periodId = $this->getRequest()->getFromPostOrUrl('period_id');
        $contextIdentifier = $this->getPresenceServiceBridge()->getContextIdentifier();
        $period = $this->getPresenceService()->findResultPeriodForPresence($this->getPresence()->getId(), $periodId, $contextIdentifier);
        if (empty($period))
        {
            $this->throwUserException('PresenceResultPeriodNotFound');
        }

        $statusId = $this->getRequest()->getFromPostOrUrl('status_id');
        if (! $this->getPresenceService()->isValidStatusId($this->getPresence(), $statusId))
        {
            $this->throwUserException('InvalidStatus');
        }
    }
}


