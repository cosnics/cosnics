<?php
namespace Chamilo\Application\Calendar\Integration\Chamilo\Core\Home;

use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRenderer;

class Block extends \Chamilo\Core\Home\BlockRendition implements CalendarRenderer
{

    /**
     *
     * @see \libraries\calendar\renderer\CalendarRenderer::get_calendar_renderer_events()
     */
    public function get_calendar_renderer_events(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $start_time, 
        $end_time)
    {
        return \Chamilo\Application\Calendar\Storage\DataManager :: get_events($renderer, $start_time, $end_time);
    }

    /**
     *
     * @see \libraries\calendar\renderer\CalendarRenderer::is_calendar_renderer_source_visible()
     */
    public function is_calendar_renderer_source_visible($source)
    {
        return true;
    }
}
