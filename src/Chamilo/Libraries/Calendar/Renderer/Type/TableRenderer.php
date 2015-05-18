<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Calendar\Renderer\Renderer;

/**
 *
 * @package application\personal_calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class TableRenderer extends Renderer
{

    /**
     *
     * @var \libraries\calendar\table\Calendar
     */
    private $calendar;

    /**
     *
     * @return \libraries\calendar\table\Calendar
     */
    public function get_calendar()
    {
        if (! isset($this->calendar))
        {
            $this->calendar = $this->initialize_calendar();
        }

        return $this->calendar;
    }

    public function set_calendar(\Chamilo\Libraries\Calendar\Table\Calendar $calendar)
    {
        $this->calendar = $calendar;
    }

    /**
     *
     * @return \libraries\calendar\table\Calendar
     */
    abstract public function initialize_calendar();
}
