<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Integration\Chamilo\Application\Calendar;

use Chamilo\Application\Calendar\Architecture\InternalCalendar;
use Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Integration\Chamilo\Application\Calendar\Service\CalendarEventDataProvider;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider;

/**
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Integration\Chamilo\Application\Calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Manager extends InternalCalendar
{

    /**
     *
     * @var CalendarEventDataProvider
     */
    protected $calendarEventDataProvider;

    public function __construct()
    {
        $this->calendarEventDataProvider = new CalendarEventDataProvider();
    }

    /**
     *
     * @see \Chamilo\Application\Calendar\CalendarInterface::getEvents()
     */
    public function getEvents(
        CalendarRendererProvider $calendarRendererProvider,
        $requestedSourceType, $fromDate, $toDate)
    {
        return $this->calendarEventDataProvider->getEvents(
            $calendarRendererProvider,
            $requestedSourceType,
            $fromDate,
            $toDate);
    }

    /**
     *
     * @see \Chamilo\Application\Calendar\Architecture\CalendarInterface::getCalendars()
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User|null $user
     *
     * @return array|\Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     */
    public function getCalendars(User $user = null)
    {
        return $this->calendarEventDataProvider->getCalendars();
    }
}
