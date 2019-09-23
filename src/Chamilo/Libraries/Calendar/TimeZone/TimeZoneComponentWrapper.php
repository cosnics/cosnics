<?php
namespace Chamilo\Libraries\Calendar\TimeZone;

use Sabre\VObject\Component;
use Sabre\VObject\Component\VCalendar;

/**
 *
 * @package Chamilo\Libraries\Calendar\TimeZone
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TimeZoneComponentWrapper extends TimeZoneCalendarWrapper
{

    /**
     *
     * @var \Sabre\VObject\Component
     */
    private $component;

    /**
     *
     * @param \Sabre\VObject\Component\VCalendar $vCalendar
     * @param \Sabre\VObject\Component $component
     */
    public function __construct(VCalendar $vCalendar, Component $component)
    {
        $this->component = $component;
        parent::__construct($vCalendar);
    }

    /**
     *
     * @return \Sabre\VObject\Component
     */
    public function getComponent()
    {
        return $this->component;
    }

    /**
     *
     * @param \Sabre\VObject\Component $component
     */
    public function setComponent(Component $component)
    {
        $this->component = $component;
    }

    /**
     *
     * @param string $name
     * @param string $value
     */
    public function setProperty($name, $value)
    {
        if ($name == 'DTSTART')
        {
            $this->getComponent()->add($name, $this->implodeDate($value));
        }
        elseif ($name == 'RDATE')
        {
            foreach ($value as $dateArray)
            {
                $this->getComponent()->add($name, $this->implodeDate($dateArray));
            }
        }
        else
        {
            $this->getComponent()->add($name, $value);
        }
    }

    /**
     *
     * @param integer[] $dateArray
     *
     * @return string
     */
    private function implodeDate($dateArray)
    {
        $date = array();

        $date[] = str_pad($dateArray['year'], 4, '0', STR_PAD_LEFT);
        $date[] = str_pad($dateArray['month'], 2, '0', STR_PAD_LEFT);
        $date[] = str_pad($dateArray['day'], 2, '0', STR_PAD_LEFT);
        $date[] = 'T';
        $date[] = str_pad($dateArray['hour'], 2, '0', STR_PAD_LEFT);
        $date[] = str_pad($dateArray['minute'], 2, '0', STR_PAD_LEFT);
        $date[] = str_pad($dateArray['second'], 2, '0', STR_PAD_LEFT);

        return implode('', $date);
    }

    public function newDaylight()
    {
        return $this->newComponent('daylight');
    }

    public function newStandard()
    {
        return $this->newComponent('standard');
    }

    /**
     *
     * @param string $componentName
     *
     * @return \Chamilo\Libraries\Calendar\TimeZone\TimeZoneComponentWrapper
     */
    public function newComponent($componentName)
    {
        $component = $this->getVCalendar()->createComponent($componentName);
        $this->getComponent()->add($component);

        return new TimeZoneComponentWrapper($this->getVCalendar(), $component);
    }
}