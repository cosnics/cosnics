<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\PresenceResultPeriod;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class DeletePresencePeriodComponent extends Manager implements CsrfComponentInterface
{
    /**
     * @var PresenceResultPeriod
     */
    protected $presenceResultPeriod;

    function run()
    {
        try
        {
            if (!$this->canUserEditPresence())
            {
                throw new NotAllowedException();
            }
            $this->validatePresenceUserInput();
            $this->getPresenceService()->deletePresenceResultPeriod($this->presenceResultPeriod);

            $result = [
                'status' => 'ok',
                'id' => (int) $this->presenceResultPeriod->getId()
            ];

            return new JsonResponse($this->serialize($result), 200, [], true);

        } catch (\Exception $ex) {
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

        if (empty($periodId))
        {
            $this->throwUserException('NoPeriodIdProvided');
        }

        $contextIdentifier = $this->getPresenceServiceBridge()->getContextIdentifier();
        $period = $this->getPresenceService()->findResultPeriodForPresence($this->getPresence()->getId(), $periodId, $contextIdentifier);
        if (empty($period))
        {
            $this->throwUserException('PresenceResultPeriodNotFound');
        }

        $this->presenceResultPeriod = $period;
    }
}

