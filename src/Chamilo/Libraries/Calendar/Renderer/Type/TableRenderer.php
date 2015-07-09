<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Calendar\Renderer\Renderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class TableRenderer extends Renderer
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
