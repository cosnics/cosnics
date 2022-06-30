<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

use Chamilo\Libraries\Calendar\Table\Calendar;

/**
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait TableRenderer
{

    private Calendar $calendar;

    /**
     * @throws \ReflectionException
     */
    public function getCalendar(): Calendar
    {
        if (!isset($this->calendar))
        {
            $this->calendar = $this->initializeCalendar();
        }

        return $this->calendar;
    }

    public function setCalendar(Calendar $calendar)
    {
        $this->calendar = $calendar;
    }

    abstract public function initializeCalendar(): Calendar;
}
