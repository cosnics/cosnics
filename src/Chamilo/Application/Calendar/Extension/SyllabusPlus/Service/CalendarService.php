<?php
namespace Chamilo\Application\Calendar\Extension\SyllabusPlus\Service;

use Chamilo\Application\Calendar\Extension\SyllabusPlus\Repository\CalendarRepository;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\SyllabusPlus\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarService
{

    /**
     *
     * @var \Chamilo\Application\Calendar\Extension\SyllabusPlus\Repository\CalendarRepository
     */
    private $calendarRepository;

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\SyllabusPlus\Repository\CalendarRepository $calendarRepository
     */
    public function __construct(CalendarRepository $calendarRepository)
    {
        $this->CalendarRepository = $calendarRepository;
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Extension\SyllabusPlus\Repository\CalendarRepository
     */
    public function getCalendarRepository()
    {
        return $this->CalendarRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\SyllabusPlus\Repository\CalendarRepository $calendarRepository
     */
    public function setCalendarRepository(CalendarRepository $calendarRepository)
    {
        $this->CalendarRepository = $calendarRepository;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer $fromDate
     * @param integer $toDate
     * @return \Chamilo\Application\Calendar\Extension\SyllabusPlus\EventResultSet
     */
    public function getEventsForUserAndBetweenDates(User $user, $fromDate, $toDate)
    {
        return $this->getCalendarRepository()->findEventsForUserAndBetweenDates($user, $fromDate, $toDate);
    }

    /**
     *
     * @return string[]
     */
    public function getWeekLabels()
    {
        $weekLabels = $this->getCalendarRepository()->findWeekLabels();
        $weeks = array();

        while ($weekLabel = $weekLabels->next_result())
        {
            $weeks[$weekLabel['week_number']] = $weekLabel['week_startdate'];
        }

        return $weeks;
    }
}