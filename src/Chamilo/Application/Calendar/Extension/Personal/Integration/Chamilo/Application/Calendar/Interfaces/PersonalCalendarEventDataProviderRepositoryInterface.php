<?php

namespace Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Interfaces;

use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 * Interface for a CalendarEventDataProviderRepository which is used by the personal calendar extension
 *
 * @package Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Interfaces
 */
interface PersonalCalendarEventDataProviderRepositoryInterface
{
    /**
     * Returns the personal calendar publications for this specific content object type
     *
     * @param DataClassRetrievesParameters $parameters
     * @param int $fromDate
     * @param int $toDate
     */
    public function getPublications(DataClassRetrievesParameters $parameters, $fromDate, $toDate);
}