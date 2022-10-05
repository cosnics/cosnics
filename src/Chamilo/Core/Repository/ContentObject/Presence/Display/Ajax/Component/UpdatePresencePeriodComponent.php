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
class UpdatePresencePeriodComponent extends Manager implements CsrfComponentInterface
{
    /**
     * @var PresenceResultPeriod
     */
    protected $presenceResultPeriod;

    /**
     * @var string
     */
    protected $presencePeriodLabel;

    /**
     * @var bool
     */
    protected $presencePeriodSelfRegistrationDisabled;

    function run()
    {
        try
        {
            if (!$this->canUserEditPresence())
            {
                throw new NotAllowedException();
            }
            $this->validatePresenceUserInput();
            $this->presenceResultPeriod->setLabel($this->presencePeriodLabel);
            $this->presenceResultPeriod->setPeriodSelfRegistrationDisabled($this->presencePeriodSelfRegistrationDisabled);
            $this->getPresenceResultPeriodService()->updatePresenceResultPeriod($this->presenceResultPeriod);

            $result = [
                'status' => 'ok',
                'id' => (int) $this->presenceResultPeriod->getId(),
                'label' => $this->presenceResultPeriod->getLabel(),
                'period_self_registration_disabled' => $this->presenceResultPeriod->isPeriodSelfRegistrationDisabled()
            ];

            return new JsonResponse($this->serialize($result), 200, [], true);

        }
        catch (\Exception $ex)
        {
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

        $periodLabel = $this->getRequest()->getFromPostOrUrl('period_label');
        $periodSelfRegistrationDisabled = $this->getRequest()->getFromPostOrUrl('period_self_registration_disabled') == 'true';

        $contextIdentifier = $this->getPresenceServiceBridge()->getContextIdentifier();
        $period = $this->getPresenceResultPeriodService()->findResultPeriodForPresence($this->getPresence(), $periodId, $contextIdentifier);
        if (empty($period))
        {
            $this->throwUserException('PresenceResultPeriodNotFound');
        }

        $this->presenceResultPeriod = $period;
        $this->presencePeriodLabel = $periodLabel;
        $this->presencePeriodSelfRegistrationDisabled = $periodSelfRegistrationDisabled;
    }
}

