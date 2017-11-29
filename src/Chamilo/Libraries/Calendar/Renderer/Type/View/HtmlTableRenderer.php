<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

use Chamilo\Libraries\Calendar\HtmlTable\Calendar;
use Chamilo\Libraries\Calendar\Renderer\Type\ViewRenderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class HtmlTableRenderer extends ViewRenderer
{

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Table\Calendar
     */
    private $calendar;

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Table\Calendar
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
}