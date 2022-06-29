<?php
namespace Chamilo\Libraries\Calendar\Event\RecurrenceRules;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event\RecurrenceRules
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RecurrenceRulesIcalParser
{
    /**
     * @return string[][]
     */
    private function getParts(string $icalRecurrenceRules): array
    {
        // Make sure everything is uppercase
        $icalRecurrenceRules = strtoupper($icalRecurrenceRules);

        // Strip the "RRULE:" part if it is still present
        $icalRecurrenceRules = str_replace('RRULE:', '', $icalRecurrenceRules);

        // Split the different parts
        $icalRecurrenceRules = explode(';', $icalRecurrenceRules);

        $parts = [];

        foreach ($icalRecurrenceRules as $rulePart)
        {
            // Split the part name and value
            list($rulePartName, $rulePartValue) = explode('=', $rulePart);

            if (in_array($rulePartName, ['FREQ', 'UNTIL', 'COUNT', 'INTERVAL']))
            {
                $parts[$rulePartName] = $rulePartValue;
            }
            else
            {
                $parts[$rulePartName] = explode(',', $rulePartValue);
            }
        }

        return $parts;
    }

    public function getRules(string $icalRecurrenceRules): RecurrenceRules
    {
        $icalRecurrenceRules = $this->getParts($icalRecurrenceRules);

        $recurrenceRules = new RecurrenceRules();

        if ($icalRecurrenceRules['FREQ'])
        {
            switch ($icalRecurrenceRules['FREQ'])
            {
                case 'DAILY' :
                    $recurrenceRules->setFrequency(RecurrenceRules::FREQUENCY_DAILY);
                    break;
                case 'WEEKLY' :
                    $recurrenceRules->setFrequency(RecurrenceRules::FREQUENCY_WEEKLY);
                    break;
                case 'MONTHLY' :
                    $recurrenceRules->setFrequency(RecurrenceRules::FREQUENCY_MONTHLY);
                    break;
                case 'YEARLY' :
                    $recurrenceRules->setFrequency(RecurrenceRules::FREQUENCY_YEARLY);
                    break;
            }

            if ($icalRecurrenceRules['COUNT'])
            {
                $recurrenceRules->setCount((int) $icalRecurrenceRules['COUNT']);
            }

            if ($icalRecurrenceRules['INTERVAL'])
            {
                $recurrenceRules->setCount((int) $icalRecurrenceRules['INTERVAL']);
            }

            if ($icalRecurrenceRules['BYDAY'])
            {
                $recurrenceRules->setByDay($icalRecurrenceRules['BYDAY']);
            }

            if ($icalRecurrenceRules['BYMONTHDAY'])
            {
                $recurrenceRules->setByMonthDay($icalRecurrenceRules['BYMONTHDAY']);
            }

            if ($icalRecurrenceRules['BYMONTH'])
            {
                $recurrenceRules->setByMonth($icalRecurrenceRules['BYMONTH']);
            }

            if ($icalRecurrenceRules['BYWEEKNO'])
            {
                $recurrenceRules->setByWeekNumber($icalRecurrenceRules['BYWEEKNO']);
            }
        }

        return $recurrenceRules;
    }
}