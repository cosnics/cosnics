<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Calendar\HtmlTable\Calendar;
use Chamilo\Libraries\Calendar\Renderer\Type\ViewRenderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class HtmlTableRenderer extends ViewRenderer
{

    /**
     *
     * @var \Chamilo\Libraries\Calendar\HtmlTable\Calendar
     */
    private $calendar;

    /**
     *
     * @return \Chamilo\Libraries\Calendar\HtmlTable\Calendar
     */
    public function getCalendar()
    {
        if (! isset($this->calendar))
        {
            $this->calendar = $this->initializeCalendar();
        }

        return $this->calendar;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\HtmlTable\Calendar $calendar
     */
    public function setCalendar(Calendar $calendar)
    {
        $this->calendar = $calendar;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Table\Calendar
     */
    abstract public function initializeCalendar();

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Table\CalendarConfiguration
     */
    protected function getCalendarConfiguration()
    {
        return $this->getService('chamilo.libraries.calendar.calendar_configuration');
    }
}