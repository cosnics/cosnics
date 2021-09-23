<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Service;

use Chamilo\Core\Repository\ContentObject\Presence\Domain\Exceptions\PresenceValidationException;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class PresenceValidationService
{
    private const COLORS = ['pink', 'blue', 'cyan', 'teal', 'green', 'light-green', 'lime', 'yellow', 'amber', 'deep-orange', 'grey'];
    private const VALUES = ['-100', '-300', '-500', '-700', '-900'];
    private const FIXED_STATUS_IDS = [1, 2, 3];
    private const ONLINE_PRESENT_STATUS_ID = 4;
    private const STATUS_TYPE_FIXED = 'fixed';
    private const STATUS_TYPE_SEMIFIXED = 'semifixed';
    private const STATUS_TYPE_CUSTOM = 'custom';

    /**
     * @param array $statuses
     * @param array $savedStatuses
     * @param array $registeredPresenceEntryStatuses
     *
     * @throws PresenceValidationException
     */
    public function validateStatuses(array $statuses, array $savedStatuses, array $registeredPresenceEntryStatuses)
    {
        $getId = function ($status) {
            return $status['id'];
        };

        $statusIds = array_map($getId, $statuses);
        foreach ($registeredPresenceEntryStatuses as $statusId)
        {
            if (! in_array($statusId, $statusIds))
            {
                throw new PresenceValidationException('PresenceStatusMissing', $statusId); // todo: specify because it's missing
            }
        }

        foreach ($statuses as $status)
        {
            $this->checkType($status);
            $this->checkTitle($status, $savedStatuses, $registeredPresenceEntryStatuses);
            $this->checkAliasses($status, $savedStatuses, $registeredPresenceEntryStatuses);
            $this->checkCode($status);
            $this->checkColor($status);
        }
    }

    /**
     * @param $status
     *
     * @throws PresenceValidationException
     */
    protected function checkType($status)
    {
        if ($status['type'] !== $this->getExpectedType($status)) {
            throw new PresenceValidationException('InvalidType', $status['id']);
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

        if ($statusId == self::ONLINE_PRESENT_STATUS_ID)
        {
            return self::STATUS_TYPE_SEMIFIXED;
        }

        if (in_array($statusId, self::FIXED_STATUS_IDS))
        {
            return self::STATUS_TYPE_FIXED;
        }

        return self::STATUS_TYPE_CUSTOM;
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

        if ($status['type'] === self::STATUS_TYPE_CUSTOM)
        {
            if (empty($title))
            {
                throw new PresenceValidationException('NoTitleGiven', $statusId);
            }

            if (in_array($statusId, $registeredPresenceEntryStatuses) && $title !== $savedStatuses[$statusId]['title'])
            {
                throw new PresenceValidationException('AttemptedTitleUpdate', $statusId);
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

        if ($status['type'] === self::STATUS_TYPE_CUSTOM)
        {
            if (!in_array($status['aliasses'], self::FIXED_STATUS_IDS))
            {
                throw new PresenceValidationException('InvalidAlias', $statusId);
            }

            if (in_array($statusId, $registeredPresenceEntryStatuses) && $status['aliasses'] !== $savedStatuses[$statusId]['aliasses'])
            {
                throw new PresenceValidationException('AttemptedAliasUpdate', $statusId);
            }
        }
    }

    /**
     * @param $status
     *
     * @throws PresenceValidationException
     */
    protected function checkCode($status)
    {
        if (empty($status['code']))
        {
            throw new PresenceValidationException('NoCodeGiven', $status['id']);
        }
    }

    /**
     * @param $status
     *
     * @throws PresenceValidationException
     */
    protected function checkColor($status)
    {
        $statusId = $status['id'];
        $color = $status['color'];

        if (empty($color))
        {
            throw new PresenceValidationException('NoColorGiven', $statusId);
        }

        if (!$this->isValidColor($color))
        {
            throw new PresenceValidationException('InvalidColor', $statusId);
        }
    }

    /**
     * @param string $color
     *
     * @return bool
     */
    protected function isValidColor(string $color): bool
    {
        return in_array(substr($color, 0, -4), self::COLORS) && in_array(substr($color, -4), self::VALUES);
    }
}