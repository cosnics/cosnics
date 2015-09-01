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

    /**
     * @param \Chamilo\Configuration\Configuration $configuration
     * @return boolean
     */
    public function isConfigured(\Chamilo\Configuration\Configuration $configuration)
    {
        $namespace = \Chamilo\Application\Calendar\Extension\SyllabusPlus\Manager :: package();

        $hasDriver = $configuration->get_setting(array($namespace, 'dbms'));
        $hasUser = $configuration->get_setting(array($namespace, 'user'));
        $hasPassword = $configuration->get_setting(array($namespace, 'password'));
        $hasHost = $configuration->get_setting(array($namespace, 'host'));
        $hasDatabase = $configuration->get_setting(array($namespace, 'database'));

        return $hasDriver && $hasUser && $hasPassword && $hasHost && $hasDatabase;
    }
}