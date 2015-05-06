<?php
namespace Chamilo\Libraries\Calendar\Renderer\Interfaces;

use Chamilo\Libraries\Calendar\Renderer\Renderer;
/**
 * An interface which forces the implementing Application to provide a given set of methods
 *
 * @package libraries\calendar\renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface CalendarRenderer
{

    /**
     * Get the events between $start_time and $end_time which should be displayed in the calendar
     *
     * @param Renderer $renderer
     * @param int $start_time
     * @param int $end_time
     * @return Event[]
     */
    public function get_calendar_renderer_events(Renderer $renderer, $start_time, $end_time);
}