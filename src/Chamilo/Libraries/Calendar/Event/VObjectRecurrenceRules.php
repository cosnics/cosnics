<?php
namespace Chamilo\Libraries\Calendar\Event;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class VObjectRecurrenceRules
{

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Event\IcalRecurrenceRules
     */
    private $iCalRecurrenceRules;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\IcalRecurrenceRules $iCalRecurrenceRules
     */
    public function __construct(IcalRecurrenceRules $iCalRecurrenceRules)
    {
        $this->iCalRecurrenceRules = $iCalRecurrenceRules;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Event\IcalRecurrenceRules
     */
    public function getICalRecurrenceRules()
    {
        return $this->iCalRecurrenceRules;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\IcalRecurrenceRules $iCalRecurrenceRules
     */
    public function setICalRecurrenceRules($iCalRecurrenceRules)
    {
        $this->iCalRecurrenceRules = $iCalRecurrenceRules;
    }

    /**
     *
     * @return string[]
     */
    public function get()
    {
        $iCalRecurrenceRules = $this->getICalRecurrenceRules();
        $iCalFormattedRecurrenceRules = $iCalRecurrenceRules->get();

        if (isset($iCalFormattedRecurrenceRules['BYDAY']))
        {
            $byDays = array();

            foreach ($iCalFormattedRecurrenceRules['BYDAY'] as $byDay)
            {
                $byDays[] = implode('', $byDay);
            }

            $iCalFormattedRecurrenceRules['BYDAY'] = implode(',', $byDays);
        }

        return $iCalFormattedRecurrenceRules;
    }
}