<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

use Chamilo\Libraries\Calendar\Renderer\Type\ViewRenderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class TableRenderer extends ViewRenderer
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

    public function setCalendar(\Chamilo\Libraries\Calendar\Table\Calendar $calendar)
    {
        $this->calendar = $calendar;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Table\Calendar
     */
    abstract public function initializeCalendar();
}
