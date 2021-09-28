<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class SavePresenceEntryComponent extends Manager  implements CsrfComponentInterface
{
    function run()
    {
        try
        {
            if (!$this->canUserEditPresence())
            {
                throw new NotAllowedException();
            }
            $this->validatePresenceUserInput();

            $periodId = $this->getRequest()->getFromPostOrUrl('period_id');
            $userId = $this->getRequest()->getFromPostOrUrl('user_id');
            $statusId = $this->getRequest()->getFromPostOrUrl('status_id');

            $presenceResultEntry = $this->getPresenceService()->createOrUpdatePresenceResultEntry($this->getPresence(), $periodId, $userId, $statusId);

            $result = [
                'status' => 'ok',
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


