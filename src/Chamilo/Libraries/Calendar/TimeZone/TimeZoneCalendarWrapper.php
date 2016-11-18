<?php
namespace Chamilo\Libraries\Calendar\TimeZone;

use Sabre\VObject\Component\VCalendar;

class TimeZoneCalendarWrapper
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

    public function getProperty()
    {
    }

    /**
     *
     * @param string $componentName
     * @return \Sabre\VObject\Component
     */
    public function newComponent($componentName)
    {
        $component = $this->getVCalendar()->createComponent($componentName);
        $this->getVCalendar()->add($component);
        return new TimeZoneComponentWrapper($this->getVCalendar(), $component);
    }
}