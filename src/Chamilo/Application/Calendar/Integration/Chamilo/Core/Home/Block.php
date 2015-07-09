<?php
namespace Chamilo\Application\Calendar\Integration\Chamilo\Core\Home;

use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface;

class Block extends \Chamilo\Core\Home\BlockRendition implements CalendarRendererProviderInterface
{

    /**
     *
     * @see \libraries\calendar\renderer\CalendarRendererProviderInterface::get_calendar_renderer_events()
     */
    public function getEvents(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $start_time, $end_time)
    {
        return \Chamilo\Application\Calendar\Storage\DataManager :: get_events($renderer, $start_time, $end_time);
    }

    /**
     *
     * @see \libraries\calendar\renderer\CalendarRendererProviderInterface::is_calendar_renderer_source_visible()
     */
    public function isSourceVisible($source)
    {
        return true;
    }

    /**
     *
     * @deprecated Provided for legacy-code
     * @see \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface::getUrl()
     */
    public function getUrl($parameters = array(), $filter = array(), $encode_entities = false)
    {
        return $this->get_url($parameters, $filter, $encode_entities);
    }
}
