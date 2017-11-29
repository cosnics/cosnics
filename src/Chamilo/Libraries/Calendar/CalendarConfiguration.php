<?php
namespace Chamilo\Libraries\Calendar;

/**
 *
 * @package Chamilo\Libraries\Calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarConfiguration
{

    /**
     *
     * @var integer
     */
    private $workingHoursStart;

    /**
     *
     * @var integer
     */
    private $workingHoursEnd;

    /**
     *
     * @var boolean
     */
    private $hideNonWorkingHours;

    /**
     *
     * @var string
     */
    private $firstDayOfTheWeek;

    /**
     *
     * @param integer $workingHoursStart
     * @param integer $workingHoursEnd
     * @param boolean $hideNonWorkingHours
     * @param string $firstDayOfTheWeek
     */
    public function __construct($workingHoursStart, $workingHoursEnd, $hideNonWorkingHours, $firstDayOfTheWeek)
    {
        $this->workingHoursStart = $workingHoursStart;
        $this->workingHoursEnd = $workingHoursEnd;
        $this->hideNonWorkingHours = $hideNonWorkingHours;
        $this->firstDayOfTheWeek = $firstDayOfTheWeek;
    }

    /**
     *
     * @return integer
     */
    public function getWorkingHoursStart()
    {
        return $this->workingHoursStart;
    }

    /**
     *
     * @param integer $workingHoursStart
     */
    public function setWorkingHoursStart($workingHoursStart)
    {
        $this->workingHoursStart = $workingHoursStart;
    }

    /**
     *
     * @return integer
     */
    public function getWorkingHoursEnd()
    {
        return $this->workingHoursEnd;
    }

    /**
     *
     * @param integer $workingHoursEnd
     */
    public function setWorkingHoursEnd($workingHoursEnd)
    {
        $this->workingHoursEnd = $workingHoursEnd;
    }

    /**
     *
     * @return boolean
     */
    public function getHideNonWorkingHours()
    {
        return $this->hideNonWorkingHours;
    }

    /**
     *
     * @param boolean $hideNonWorkingHours
     */
    public function setHideNonWorkingHours($hideNonWorkingHours)
    {
        $this->hideNonWorkingHours = $hideNonWorkingHours;
    }

    /**
     *
     * @return string
     */
    public function getFirstDayOfTheWeek()
    {
        return $this->firstDayOfTheWeek;
    }

    /**
     *
     * @param string $firstDayOfTheWeek
     */
    public function setFirstDayOfTheWeek($firstDayOfTheWeek)
    {
        $this->firstDayOfTheWeek = $firstDayOfTheWeek;
    }
}

