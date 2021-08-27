<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Service;

use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\PresenceResultEntry;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Core\Repository\ContentObject\Presence\Display\Storage\Repository\PresenceRepository;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use http\Exception\InvalidArgumentException;
use JMS\Serializer\Serializer;
use DateTime;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class PresenceService
{
    /**
     * @var PresenceRepository
     */
    protected $presenceRepository;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @param PresenceRepository $presenceRepository
     * @param Serializer $serializer
     */
    public function __construct(PresenceRepository $presenceRepository, Serializer $serializer)
    {
        $this->presenceRepository = $presenceRepository;
        $this->serializer = $serializer;
    }

    /**
     * @param int $presenceId
     * @param ContextIdentifier $contextIdentifier
     * @return array
     */
    public function getResultPeriodsForPresence(int $presenceId, ContextIdentifier $contextIdentifier): array
    {
        $periods = $this->presenceRepository->getResultPeriodsForPresence($presenceId, $contextIdentifier);
        $periods = iterator_to_array($periods);
        foreach ($periods as $index => $period) {
            $periods[$index]['date'] = (int)$period['date'];
            $periods[$index]['id'] = (int)$period['id'];
        }
        return $periods;
    }

    /**
     * @param int $presenceId
     * @param $periodId
     * @param ContextIdentifier $contextIdentifier
     * @return CompositeDataClass|DataClass|false
     */
    public function findResultPeriodForPresence(int $presenceId, $periodId, ContextIdentifier $contextIdentifier)
    {
        return $this->presenceRepository->findResultPeriodForPresence($presenceId, $periodId, $contextIdentifier);
    }

    /**
     * @param Presence $presence
     * @param int $statusId
     * @return bool
     */
    public function isValidStatusId(Presence $presence, int $statusId): bool
    {
        $options = $this->getPresenceOptions($presence);
        return in_array($statusId, array_column($options, 'id'));
    }

    /**
     * @param Presence $presence
     * @param int $periodId
     * @param int $userId
     * @param int $statusId
     * @return PresenceResultEntry
     */
    public function createOrUpdatePresenceResultEntry(Presence $presence, int $periodId, int $userId, int $statusId): PresenceResultEntry
    {
        $fixedStatusId = $this->findFixedStatusId($presence, $statusId);
        if ($fixedStatusId == -1)
        {
            throw new InvalidArgumentException();
        }

        $presenceResultEntry = $this->presenceRepository->getPresenceResultEntry($periodId, $userId);
        if (! $presenceResultEntry instanceof PresenceResultEntry)
        {
            $presenceResultEntry = $this->createPresenceResultEntry($periodId, $userId);
            $presenceResultEntry = $this->updatePresenceResultEntryFields($presenceResultEntry, $statusId, $fixedStatusId);
            $this->presenceRepository->createPresenceResultEntry($presenceResultEntry);
        }
        else
        {
            $presenceResultEntry = $this->updatePresenceResultEntryFields($presenceResultEntry, $statusId, $fixedStatusId);
            $this->presenceRepository->updatePresenceResultEntry($presenceResultEntry);
        }
        return $presenceResultEntry;
    }

    /**
     * @param int $periodId
     * @param int $userId
     * @return PresenceResultEntry
     */
    protected function createPresenceResultEntry(int $periodId, int $userId): PresenceResultEntry
    {
        $presenceResultEntry = new PresenceResultEntry();
        $presenceResultEntry->setPresencePeriodId($periodId);
        $presenceResultEntry->setUserId($userId);
        return $presenceResultEntry;
    }

    /**
     * @param PresenceResultEntry $presenceResultEntry
     * @param int $statusId
     * @param int $fixedStatusId
     */
    protected function updatePresenceResultEntryFields(PresenceResultEntry $presenceResultEntry, int $statusId, int $fixedStatusId): PresenceResultEntry
    {
        $presenceResultEntry->setChoiceId($statusId);
        $presenceResultEntry->setPresenceStatusId($fixedStatusId);
        $presenceResultEntry->setCheckedInDate($fixedStatusId == 3 ? (new DateTime())->getTimestamp() : 0);
        $presenceResultEntry->setCheckedOutDate(0);
        return $presenceResultEntry;
    }

    /**
     * @param Presence $presence
     * @param int $statusId
     * @return int
     */
    protected function findFixedStatusId(Presence $presence, int $statusId): int
    {
        if (in_array($statusId, [1, 2, 3]))
        {
            return $statusId;
        }
        if ($statusId == 4)
        {
            return 3;
        }
        $options = $this->getPresenceOptions($presence);
        $index = array_search($statusId, array_column($options, 'id'));
        if ($index !== false)
        {
            $fixedId = $options[$index]['aliasses'];
            if (in_array($fixedId, [1, 2, 3]))
            {
                return $fixedId;
            }
        }
        return -1;
    }

    /**
     * @param Presence $presence
     * @return array
     */
    protected function getPresenceOptions(Presence $presence): array
    {
        return $this->serializer->deserialize($presence->getOptions(), 'array', 'json');
    }
}