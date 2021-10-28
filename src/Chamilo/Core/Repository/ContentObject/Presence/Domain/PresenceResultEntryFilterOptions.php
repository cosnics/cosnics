<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Domain;

/**
 * Class PresenceResultEntryFilterOptions
 * @package Chamilo\Core\Repository\ContentObject\Presence\Domain
 *
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class PresenceResultEntryFilterOptions
{
    public int $periodId = -1;
    public array $statusFilters = array(); // int[]
    public bool $withoutStatus = false;
}