<?php
namespace Chamilo\Libraries\Calendar\Event\RecurrenceRules;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event\RecurrenceRules$RecurrenceRules
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RecurrenceRules
{
    const FREQUENCY_BIWEEKLY = 4;
    const FREQUENCY_DAILY = 1;
    const FREQUENCY_MONTHLY = 5;
    const FREQUENCY_NONE = 0;
    const FREQUENCY_WEEKDAYS = 3;
    const FREQUENCY_WEEKLY = 2;
    const FREQUENCY_YEARLY = 6;

    /**
     *
     * @var integer
     */
    private $frequency;

    /**
     *
     * @var integer
     */
    private $until;

    /**
     *
     * @var integer
     */
    private $count;

    /**
     *
     * @var integer
     */
    private $interval;

    /**
     *
     * @var string[]
     */
    private $byDay;

    /**
     *
     * @var integer[]
     */
    private $byMonthDay;

    /**
     *
     * @var integer[]
     */
    private $byMonth;

    /**
     *
     * @var integer[]
     */
    private $byWeekNumber;

    /**
     *
     * @param integer $frequency
     * @param integer $until
     * @param integer $count
     * @param integer $interval
     * @param string[] $byDay
     * @param integer[] $byMonthDay
     * @param integer[] $byMonth
     * @param integer[] $byWeekNumber
     */
    public function __construct(
        $frequency = self::FREQUENCY_NONE, $until = null, $count = null, $interval = null, $byDay = array(),
        $byMonthDay = array(), $byMonth = array(), $byWeekNumber = array()
    )
    {
        $this->frequency = $frequency;
        $this->until = $until;
        $this->count = $count;
        $this->interval = $interval;
        $this->byDay = $byDay;
        $this->byMonthDay = $byMonthDay;
        $this->byMonth = $byMonth;
        $this->byWeekNumber = $byWeekNumber;
    }

    /**
     *
     * @return string[]
     */
    public function getByDay()
    {
        return $this->byDay;
    }

    /**
     *
     * @param string[] $byDay
     */
    public function setByDay($byDay)
    {
        $this->byDay = $byDay;
    }

    /**
     *
     * @return integer[]
     */
    public function getByMonth()
    {
        return $this->byMonth;
    }

    /**
     *
     * @param integer[] $byMonth
     */
    public function setByMonth($byMonth)
    {
        $this->byMonth = $byMonth;
    }

    /**
     *
     * @return integer[]
     */
    public function getByMonthDay()
    {
        return $this->byMonthDay;
    }

    /**
     *
     * @param integer[] $byMonthDay
     */
    public function setByMonthDay($byMonthDay)
    {
        $this->byMonthDay = $byMonthDay;
    }

    /**
     *
     * @return integer[]
     */
    public function getByWeekNumber()
    {
        return $this->byWeekNumber;
    }

    /**
     *
     * @param integer[] $byWeekNumber
     */
    public function setByWeekNumber($byWeekNumber)
    {
        $this->byWeekNumber = $byWeekNumber;
    }

    /**
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     *
     * @param integer $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     *
     * @return integer
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     *
     * @param integer $frequency
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;
    }

    /**
     *
     * @return integer
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     *
     * @param integer $interval
     */
    public function setInterval($interval)
    {
        $this->interval = $interval;
    }

    /**
     *
     * @return integer
     */
    public function getUntil()
    {
        return $this->until;
    }

    /**
     *
     * @param integer $until
     */
    public function setUntil($until)
    {
        $this->until = $until;
    }

    /**
     *
     * @return boolean
     */
    public function hasRecurrence()
    {
        return $this->getFrequency() != self::FREQUENCY_NONE;
    }

    /**
     *
     * @return boolean
     */
    public function isIndefinite()
    {
        $repeatTo = $this->getUntil();

        return ($repeatTo == 0 || is_null($repeatTo));
    }
}