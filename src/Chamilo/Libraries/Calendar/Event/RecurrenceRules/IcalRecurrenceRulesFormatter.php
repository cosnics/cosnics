<?php
namespace Chamilo\Libraries\Calendar\Event\RecurrenceRules;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event\RecurrenceRules
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class IcalRecurrenceRulesFormatter
{

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\RecurrenceRules\RecurrenceRules $recurrenceRules
     *
     * @return string[][]
     */
    public function format(RecurrenceRules $recurrenceRules)
    {
        $iCalRules = array();

        switch ($recurrenceRules->getFrequency())
        {
            case RecurrenceRules::FREQUENCY_DAILY :
                $iCalRules['FREQ'] = 'DAILY';
                break;
            case RecurrenceRules::FREQUENCY_WEEKLY :
                $iCalRules['FREQ'] = 'WEEKLY';
                break;
            case RecurrenceRules::FREQUENCY_MONTHLY :
                $iCalRules['FREQ'] = 'MONTHLY';
                break;
            case RecurrenceRules::FREQUENCY_YEARLY :
                $iCalRules['FREQ'] = 'YEARLY';
                break;
            case RecurrenceRules::FREQUENCY_BIWEEKLY :
                $iCalRules['FREQ'] = 'WEEKLY';
                $iCalRules['INTERVAL'] = '2';
                break;
            case RecurrenceRules::FREQUENCY_WEEKDAYS :
                $iCalRules['FREQ'] = 'DAILY';
                $iCalRules['BYDAY'] = array(
                    array('DAY' => 'MO'),
                    array('DAY' => 'TU'),
                    array('DAY' => 'WE'),
                    array('DAY' => 'TH'),
                    array('DAY' => 'FR')
                );
                break;
        }

        if (!$recurrenceRules->isIndefinite())
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
            $iCalRules['BYDAY'] = $this->getByDayParts($recurrenceRules->getByDay());
        }

        if ($recurrenceRules->getByMonthDay())
        {
            $iCalRules['BYMONTHDAY'] = implode(',', $recurrenceRules->getByMonthDay());
        }

        if ($recurrenceRules->getByMonth())
        {
            $iCalRules['BYMONTH'] = implode(',', $recurrenceRules->getByMonth());
        }

        if ($recurrenceRules->getByWeekNumber())
        {
            $iCalRules['BYWEEKNO'] = implode(',', $recurrenceRules->getByWeekNumber());
        }

        return $iCalRules;
    }

    /**
     *
     * @param string[] $byDays
     *
     * @return string[]
     */
    private function getByDayParts($byDays)
    {
        $parts = array();

        foreach ($byDays as $byDay)
        {
            preg_match_all('/(-?[1-5]?)([A-Z]+)/', $byDay, $byDayParts);
            $parts[] = array($byDayParts[1] == 0 ? 0 : $byDayParts[1][0], $byDayParts[2][0]);
        }

        return $parts;
    }

    /**
     *
     * @param integer $date
     *
     * @return string
     */
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