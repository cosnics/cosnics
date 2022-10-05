<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Service;

use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\PresenceResultPeriod;
use Chamilo\Core\Repository\ContentObject\Presence\Display\Storage\Repository\PresenceRepository;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use DateTime;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class PresenceResultPeriodService
{
    /**
     * @var PresenceRepository
     */
    protected $presenceRepository;

    /**
     * @param PresenceRepository $presenceRepository
     */
    public function __construct(PresenceRepository $presenceRepository)
    {
        $this->presenceRepository = $presenceRepository;
    }

    /**
     * @param Presence $presence
     * @param ContextIdentifier $contextIdentifier
     * @param bool $createPeriodIfEmpty
     *
     * @return array
     */
    public function getResultPeriodsForPresence(Presence $presence, ContextIdentifier $contextIdentifier, bool $createPeriodIfEmpty = false): array
    {
        $periods = $this->presenceRepository->getResultPeriodsForPresence($presence->getId(), $contextIdentifier);
        $periods = iterator_to_array($periods);
        foreach ($periods as $index => $period)
        {
            $periods[$index]['date'] = (int) $period['date'];
            $periods[$index]['id'] = (int) $period['id'];
            $periods[$index]['period_self_registration_disabled'] = (bool) $period['period_self_registration_disabled'];
        }
        if ($createPeriodIfEmpty && count($periods) == 0)
        {
            $period = $this->createPresenceResultPeriod($presence, $contextIdentifier);
            $periods = [['date' => $period->getDate(), 'id' => (int) $period->getId(), 'period_self_registration_disabled' => (bool) $period->isPeriodSelfRegistrationDisabled()]];
        }
        return $periods;
    }

    /**
     * @param Presence $presence
     * @param ContextIdentifier $contextIdentifier
     * @param string $label
     * @return PresenceResultPeriod
     */
    public function createPresenceResultPeriod(Presence $presence, ContextIdentifier $contextIdentifier, string $label = ''): PresenceResultPeriod
    {
        $presenceResultPeriod = new PresenceResultPeriod();
        $presenceResultPeriod->setPresenceId($presence->getId());
        $presenceResultPeriod->setLabel($label);
        $presenceResultPeriod->setDate((new DateTime())->getTimestamp());
        $presenceResultPeriod->setContextClass($contextIdentifier->getContextClass());
        $presenceResultPeriod->setContextId($contextIdentifier->getContextId());
        $presenceResultPeriod->setPeriodSelfRegistrationDisabled(false);

        $this->presenceRepository->createPresenceResultPeriod($presenceResultPeriod);

        return $presenceResultPeriod;
    }

    /**
     * @param Presence $presence
     * @param int $periodId
     * @param ContextIdentifier $contextIdentifier
     * @return CompositeDataClass|DataClass|false
     */
    public function findResultPeriodForPresence(Presence $presence, int $periodId, ContextIdentifier $contextIdentifier)
    {
        return $this->presenceRepository->findResultPeriodForPresence($presence->getId(), $periodId, $contextIdentifier);
    }

    /**
     * @param PresenceResultPeriod $presenceResultPeriod
     */
    public function updatePresenceResultPeriod(PresenceResultPeriod $presenceResultPeriod)
    {
        $this->presenceRepository->updatePresenceResultPeriod($presenceResultPeriod);
    }

    /**
     * @param PresenceResultPeriod $presenceResultPeriod
     */
    public function deletePresenceResultPeriod(PresenceResultPeriod $presenceResultPeriod)
    {
        $this->presenceRepository->deletePresenceResultPeriod($presenceResultPeriod);
    }
}
