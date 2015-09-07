<?php
namespace Chamilo\Libraries\Calendar\Event\RecurrenceRules;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class VObjectRecurrenceRulesFormatter extends IcalRecurrenceRulesFormatter
{

    /**
     *
     * @return string[]
     */
    public function format(RecurrenceRules $recurrenceRules)
    {
        $iCalRules = parent :: format($recurrenceRules);

        if (isset($iCalRules['BYDAY']))
        {
            $byDays = array();

            foreach ($iCalRules['BYDAY'] as $byDay)
            {
                $byDays[] = implode('', $byDay);
            }

            $iCalRules['BYDAY'] = implode(',', $byDays);
        }

        return $iCalRules;
    }
}