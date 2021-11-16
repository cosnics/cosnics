<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Service;

use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;
use Chamilo\Core\Repository\ContentObject\Presence\Display\Storage\Repository\PresenceRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;

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
     * @param Presence $presence
     *
     * @return array
     */
    public function getPresenceStatuses(Presence $presence): array
    {
        $currentStatuses = [];
        $statuses = $this->getPresenceOptions($presence);
        foreach ($statuses as $status)
        {
            $id = $status['id'];
            switch ($id)
            {
                case 1:
                    $status['title'] = 'Absent / Afwezig';
                    break;
                case 2:
                    $status['title'] = 'Authorized absent / Gewettigd afwezig';
                    break;
                case 3:
                    $status['title'] = 'Present / Aanwezig';
                    break;
                case 4:
                    $status['title'] = 'Online present / Online aanwezig';
                    break;
            }
            $currentStatuses[$id] = $status;
        }
        return $currentStatuses;
    }

    /**
     * @param int $presenceId
     *
     * @return array
     */
    public function getRegisteredPresenceEntryStatuses(int $presenceId): array
    {
        $statuses = $this->presenceRepository->getRegisteredPresenceEntryStatuses($presenceId);
        $lst = [];
        foreach ($statuses as $status)
        {
            $lst[] = (int) $status['choice_id'];
        }
        return $lst;
    }

    /**
     * @param Presence $presence
     * @param int $statusId
     *
     * @return bool
     */
    public function isValidStatusId(Presence $presence, int $statusId): bool
    {
        $options = $this->getPresenceOptions($presence);
        return in_array($statusId, array_column($options, 'id'));
    }


    /**
     * @param Presence $presence
     * @param int $statusId
     * @return int
     */
    public function findFixedStatusId(Presence $presence, int $statusId): int
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
    public function getPresenceOptions(Presence $presence): array
    {
        return $this->serializer->deserialize($presence->getOptions(), 'array', 'json');
    }

    /**
     * @param Presence $presence
     * @param array $options
     * @param array $verifyIcon
     * @param bool $hasCheckout
     *
     * @throws \Exception
     */
    public function setPresenceOptions(Presence $presence, array $options, array $verifyIcon, bool $hasCheckout = false)
    {
        $context = $this->createSerializationContext();
        $presence->setOptions($this->serializer->serialize($options, 'json', $context));
        $context = $this->createSerializationContext();
        $presence->setVerifyIcon(empty($verifyIcon) ? '' : $this->serializer->serialize($verifyIcon, 'json', $context));
        $presence->setHasCheckout($hasCheckout);
        $presence->update();
    }

    /**
     * @return SerializationContext
     */
    protected function createSerializationContext(): SerializationContext
    {
        $context = new SerializationContext();
        $context->setSerializeNull(true);
        return $context;
    }

}