<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Interfaces;

use Chamilo\Libraries\Storage\StorageParameters;

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
     * @param StorageParameters $parameters
     * @param int $fromDate
     * @param int $toDate
     *
     * @return \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication[]
     */
    public function getPublications(StorageParameters $parameters, $fromDate, $toDate);
}