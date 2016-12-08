<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Integration\Chamilo\Application\Calendar;

use Chamilo\Application\Calendar\Architecture\InternalCalendar;
use Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Integration\Chamilo\Application\Calendar\Service\CalendarEventDataProvider;

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
        \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider, 
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
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     */
    public function getCalendars()
    {
        return $this->calendarEventDataProvider->getCalendars();
    }
}
