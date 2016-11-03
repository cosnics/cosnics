<?php
namespace Chamilo\Application\Calendar\Architecture;

/**
 *
 * @package Chamilo\Application\Calendar\Architecture
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface CalendarInterface
{

    /**
     * Gets the events published in the implementing context
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider
     * @param integer $requestedSourceType
     * @param integer $fromDate
     * @param integer $toDate
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getEvents(
        \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider,
        $requestedSourceType, $fromDate, $toDate);

    /**
     * Get the individual calendars in the implementing context
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     */
    public function getCalendars();

    /**
     * Get the source type of the implementing context
     *
     * @return integer
     */
    public function getSourceType();
}