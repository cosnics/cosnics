<?php
namespace Chamilo\Libraries\Calendar\Service\Recurrence;

use Chamilo\Libraries\Calendar\Event\RecurrenceRules;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event\RecurrenceRules
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class VObjectRecurrenceRulesFormatter extends IcalRecurrenceRulesFormatter
{

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\RecurrenceRules $recurrenceRules
     *
     * @return string[]
     */
    public function format(RecurrenceRules $recurrenceRules): array
    {
        $iCalRules = parent::format($recurrenceRules);

        if (isset($iCalRules['BYDAY']))
        {
            $byDays = [];

            foreach ($iCalRules['BYDAY'] as $byDay)
            {
                $byDays[] = implode('', $byDay);
            }

            $iCalRules['BYDAY'] = implode(',', $byDays);
        }

        return $iCalRules;
    }
}