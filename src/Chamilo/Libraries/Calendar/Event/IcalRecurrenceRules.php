<?php
namespace Chamilo\Libraries\Calendar\Event;

class IcalRecurrenceRules
{

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Event\RecurrenceRules
     */
    private $recurrenceRules;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\RecurrenceRules $recurrenceRules
     */
    public function __construct(RecurrenceRules $recurrenceRules)
    {
        $this->recurrenceRules = $recurrenceRules;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Event\RecurrenceRules
     */
    public function getRecurrenceRules()
    {
        return $this->recurrenceRules;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\RecurrenceRules $recurrenceRules
     */
    public function setRecurrenceRules($recurrenceRules)
    {
        $this->recurrenceRules = $recurrenceRules;
    }

    /**
     *
     * @return string[]
     */
    public function get()
    {
        $recurrenceRules = $this->getRecurrenceRules();

        $iCalRules = array();

        switch ($recurrenceRules->getFrequency())
        {
            case RecurrenceRules :: FREQUENCY_DAILY :
                $iCalRules['FREQ'] = 'DAILY';
                break;
            case RecurrenceRules :: FREQUENCY_WEEKLY :
                $iCalRules['FREQ'] = 'WEEKLY';
                break;
            case RecurrenceRules :: FREQUENCY_MONTHLY :
                $iCalRules['FREQ'] = 'MONTHLY';
                break;
            case RecurrenceRules :: FREQUENCY_YEARLY :
                $iCalRules['FREQ'] = 'YEARLY';
                break;
            case RecurrenceRules :: FREQUENCY_BIWEEKLY :
                $iCalRules['FREQ'] = 'WEEKLY';
                $iCalRules['INTERVAL'] = '2';
                break;
            case RecurrenceRules :: FREQUENCY_WEEKDAYS :
                $iCalRules['FREQ'] = 'DAILY';
                $iCalRules['BYDAY'] = array(
                    array('DAY' => 'MO'),
                    array('DAY' => 'TU'),
                    array('DAY' => 'WE'),
                    array('DAY' => 'TH'),
                    array('DAY' => 'FR'));
                break;
        }

        if (! $recurrenceRules->isIndefinate())
        {
            $iCalRules['UNTIL'] = $this->getDateInIcalFormat($recurrenceRules->getUntil());
        }

        if ($recurrenceRules->getCount() > 0)
        {
            $iCalRules['COUNT'] = $recurrenceRules->getCount();
        }

        if ($recurrenceRules->getInterval() > 0)
        {
            $iCalRules['INTERVAL'] = $recurrenceRules->getInterval();
        }

        if ($recurrenceRules->getByDay())
        {
            $iCalRules['BYDAY'] = $this->getByDayParts();
        }

        if ($recurrenceRules->getByMonthDay())
        {
            $iCalRules['BYMONTHDAY'] = implode(',', $recurrenceRules->getByMonthDay());
        }

        if ($recurrenceRules->getByMonth())
        {
            $iCalRules['BYMONTH'] = implode(',', $recurrenceRules->getByMonth());
        }

        return $iCalRules;
    }

    /**
     *
     * @return boolean
     */
    private function isIndefinate()
    {
        $repeatTo = $this->getUntil();
        return ($repeatTo == 0 || is_null($repeatTo));
    }

    /**
     *
     * @return string[]
     */
    private function getByDayParts()
    {
        $byDays = $this->getRecurrenceRules()->getByDay();
        $parts = array();

        foreach ($byDays as $byDay)
        {
            preg_match_all('/(-?[1-5]?)([A-Z]+)/', $byDay, $byDayParts);
            $parts[] = array($byDayParts[1] == 0 ? 0 : $byDayParts[1][0], $byDayParts[2][0]);
        }

        return $parts;
    }

    private function getDateInIcalFormat($date)
    {
        $y = date('Y', $date);
        $m = date('m', $date);
        $d = date('d', $date);
        $h = date('H', $date);
        $M = date('i', $date);
        $s = date('s', $date);

        return $y . $m . $d . 'T' . $h . $M . $s;
    }
}