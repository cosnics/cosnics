<?php
namespace Chamilo\Libraries\Calendar\TimeZone;

use Sabre\VObject\Component\VCalendar;

/**
 *
 * @package Chamilo\Libraries\Calendar\TimeZone
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TimeZoneCalendarWrapper extends \kigkonsult\iCalcreator\vcalendar
{

    /**
     *
     * @var \Sabre\VObject\Component\VCalendar
     */
    private $vCalendar;

    /**
     *
     * @param \Sabre\VObject\Component\VCalendar $vCalendar
     */
    public function __construct(VCalendar $vCalendar)
    {
        $this->vCalendar = $vCalendar;
    }

    public function getProperty($propName = false, $propix = false, $inclParam = false)
    {
    }

    /**
     *
     * @return \Sabre\VObject\Component\VCalendar
     */
    public function getVCalendar()
    {
        return $this->vCalendar;
    }

    /**
     *
     * @param \Sabre\VObject\Component\VCalendar $vCalendar
     */
    public function setVCalendar(VCalendar $vCalendar)
    {
        $this->vCalendar = $vCalendar;
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
        $this->getVCalendar()->add($component);

        return new TimeZoneComponentWrapper($this->getVCalendar(), $component);
    }

    public function newVtimezone()
    {
        return $this->newComponent('vtimezone');
    }
}