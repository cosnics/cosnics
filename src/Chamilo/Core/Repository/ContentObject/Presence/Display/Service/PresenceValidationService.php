<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Service;

use Chamilo\Core\Repository\ContentObject\Presence\Domain\Exceptions\PresenceValidationException;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class PresenceValidationService
{
    /**
     * @var PresenceService
     */
    protected $presenceService;

    /**
     * @param PresenceService $presenceService
     */
    public function __construct(PresenceService $presenceService)
    {
        $this->presenceService = $presenceService;
    }

    /**
     * @param Presence $presence
     * @param array $statuses
     *
     * @throws PresenceValidationException
     */
    public function validateStatuses(Presence $presence, array $statuses)
    {
        $statusIds = $this->getStatusIds($statuses);
        $savedStatuses = $this->presenceService->getPresenceStatuses($presence);
        $registeredPresenceEntryStatuses = $this->presenceService->getRegisteredPresenceEntryStatuses($presence->getId());

        foreach ($registeredPresenceEntryStatuses as $statusId)
        {
            if (! in_array($statusId, $statusIds))
            {
                throw new PresenceValidationException('PresenceStatusMissing', $statusId, $savedStatuses[$statusId]); // todo: specify because it's missing
            }
        }

        foreach ($statuses as $status)
        {
            $this->checkType($status, $savedStatuses);
            $this->checkTitle($status, $savedStatuses, $registeredPresenceEntryStatuses);
            $this->checkAliasses($status, $savedStatuses, $registeredPresenceEntryStatuses);
            $this->checkCode($status, $savedStatuses);
            $this->checkColor($status, $savedStatuses);
        }
    }

    /**
     * @param array $statuses
     *
     * @return array
     */
    protected function getStatusIds(array $statuses): array
    {
        $getId = function ($status) {
            return $status['id'];
        };

        return array_map($getId, $statuses);
    }

    /**
     * @param $status
     * @param $savedStatuses
     *
     * @throws PresenceValidationException
     */
    protected function checkType($status, $savedStatuses)
    {
        $statusId = $status['id'];

        if ($status['type'] !== $this->getExpectedType($status)) {
            throw new PresenceValidationException('InvalidType', $statusId, $savedStatuses[$statusId] ?? $status);
        }
    }

    /**
     * @param array $status
     *
     * @return string
     */
    protected function getExpectedType(array $status): string
    {
        $statusId = $status['id'];

        if ($statusId == Presence::ONLINE_PRESENT_STATUS_ID)
        {
            return Presence::STATUS_TYPE_SEMIFIXED;
        }

        if (in_array($statusId, Presence::FIXED_STATUS_IDS))
        {
            return Presence::STATUS_TYPE_FIXED;
        }

        return Presence::STATUS_TYPE_CUSTOM;
    }

    /**
     * @param $status
     * @param $savedStatuses
     * @param $registeredPresenceEntryStatuses
     *
     * @throws PresenceValidationException
     */
    protected function checkTitle($status, $savedStatuses, $registeredPresenceEntryStatuses)
    {
        $statusId = $status['id'];
        $title = $status['title'];

        if ($status['type'] === Presence::STATUS_TYPE_CUSTOM)
        {
            if (empty($title))
            {
                throw new PresenceValidationException('NoTitleGiven', $statusId, $savedStatuses[$statusId] ?? $status);
            }

            if (in_array($statusId, $registeredPresenceEntryStatuses) && $title !== $savedStatuses[$statusId]['title'])
            {
                throw new PresenceValidationException('TitleUpdateForbidden', $statusId, $savedStatuses[$statusId]);
            }
        }
    }

    /**
     * @param $status
     * @param $savedStatuses
     * @param $registeredPresenceEntryStatuses
     *
     * @throws PresenceValidationException
     */
    protected function checkAliasses($status, $savedStatuses, $registeredPresenceEntryStatuses)
    {
        $statusId = $status['id'];

        if ($status['type'] === Presence::STATUS_TYPE_CUSTOM)
        {
            if (in_array($statusId, $registeredPresenceEntryStatuses) && $status['aliasses'] !== $savedStatuses[$statusId]['aliasses'])
            {
                throw new PresenceValidationException('AliasUpdateForbidden', $statusId, $savedStatuses[$statusId]);
            }

            if (! in_array($status['aliasses'], Presence::FIXED_STATUS_IDS))
            {
                throw new PresenceValidationException('InvalidAlias', $statusId, $savedStatuses[$statusId] ?? $status);
            }
        }
    }

    /**
     * @param $status
     * @param $savedStatuses
     *
     * @throws PresenceValidationException
     */
    protected function checkCode($status, $savedStatuses)
    {
        $statusId = $status['id'];

        if (empty($status['code']))
        {
            throw new PresenceValidationException('NoCodeGiven', $statusId, $savedStatuses[$statusId] ?? $status);
        }
    }

    /**
     * @param $status
     * @param $savedStatuses
     *
     * @throws PresenceValidationException
     */
    protected function checkColor($status, $savedStatuses)
    {
        $statusId = $status['id'];
        $color = $status['color'];

        if (empty($color))
        {
            throw new PresenceValidationException('NoColorGiven', $statusId, $savedStatuses[$statusId] ?? $status);
        }

        if (!$this->isValidColor($color))
        {
            throw new PresenceValidationException('InvalidColor', $statusId, $savedStatuses[$statusId] ?? $status);
        }
    }

    /**
     * @param string $color
     *
     * @return bool
     */
    protected function isValidColor(string $color): bool
    {
        return in_array(substr($color, 0, -4), Presence::COLORS) && in_array(substr($color, -4), Presence::VALUES);
    }
}