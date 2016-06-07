<?php

namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Integration\Chamilo\Application\Calendar\Extension\Personal\Repository;

use Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Interfaces\PersonalCalendarEventDataProviderRepositoryInterface;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 * Retrieves personal calendar publications for this specific content object type
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CalendarEventDataProviderRepository implements PersonalCalendarEventDataProviderRepositoryInterface
{
    /**
     * Returns the personal calendar publications for this specific content object type
     *
     * @param DataClassRetrievesParameters $parameters
     * @param int $fromDate
     * @param int $toDate
     */
    public function getPublications(DataClassRetrievesParameters $parameters, $fromDate, $toDate)
    {
        
    }
}