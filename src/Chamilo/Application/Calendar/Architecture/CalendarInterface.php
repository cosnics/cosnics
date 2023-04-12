<?php
namespace Chamilo\Application\Calendar\Architecture;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Calendar\Service\CalendarRendererProvider;

/**
 * @package Chamilo\Application\Calendar\Architecture
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
interface CalendarInterface
{

    /**
     * Get the individual calendars in the implementing context
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     */
    public function getCalendars(?User $user = null): array;

    /**
     * Gets the events published in the implementing context
     *
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getEvents(CalendarRendererProvider $calendarRendererProvider, int $fromDate, int $toDate): array;
}