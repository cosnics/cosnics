<?php
namespace Chamilo\Libraries\Calendar\Table\Type;

/**
 *
 * @package Chamilo\Libraries\Calendar\Table\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniDayCalendar extends DayCalendar
{

    /**
     *
     * @param integer $displayTime
     * @param integer $hourStep
     * @param integer $startHour
     * @param integer $endHour
     * @param boolean $hideOtherHours
     */
    public function __construct($displayTime, $hourStep = 1, $startHour = 0, $endHour = 24, $hideOtherHours = false)
    {
        parent::__construct($displayTime, $hourStep, $startHour, $endHour, $hideOtherHours);
        $this->updateAttributes('class="calendar_table mini_calendar"');
    }

    /**
     *
     * @param integer $hour
     * @return string
     */
    public function getCellIdentifier($hour)
    {
        return $hour . 'u - ' . ($hour + $this->getHourStep()) . 'u <br />';
    }

    /**
     *
     * @return string
     */
    public function getNavigationClasses()
    {
        return parent::getNavigationClasses() . ' mini_calendar';
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Table\Type\DayCalendar::render()
     */
    public function render()
    {
        $this->addEvents();
        return $this->toHtml();
    }
}
