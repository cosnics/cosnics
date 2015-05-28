<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Calendar\Renderer\Event\StartEndDateEventRenderer;
use Chamilo\Libraries\Calendar\Renderer\Renderer;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package application\personal_calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniListRenderer extends Renderer
{

    /**
     *
     * @see \application\personal_calendar\Renderer::render()
     */
    public function render()
    {
        $html = array();

        // Today's events, from the current time until midnight
        $from_time = time();
        $to_time = strtotime('tomorrow -1 second', time());
        $events = $this->get_events($this, $from_time, $to_time);
        $html[] = $this->render_events($events, 'Today', $from_time, $to_time);

        // Tomorrow's events, from midnight tomorrow until midnight the day after tomorrow
        $from_time = strtotime('tomorrow', time());
        $to_time = strtotime('tomorrow +1 day -1 second', time());
        $events = $this->get_events($this, $from_time, $to_time);
        $html[] = $this->render_events($events, 'Tomorrow', $from_time, $to_time);

        // Events that will happen soon, from midnight the day after tomorrow untill next week midnight
        $from_time = strtotime('tomorrow +1 day', time());
        $to_time = strtotime('tomorrow +7 days -1 second', time());
        $events = $this->get_events($this, $from_time, $to_time);
        $html[] = $this->render_events($events, 'Soon', $from_time, $to_time);
        $html[] = $this->build_legend();

        return implode(PHP_EOL, $html);
    }

    public function render_events($events, $type, $from_time, $to_time)
    {
        $output = array();

        if (count($events) > 0)
        {
            $output[] = '<h4>' . Translation :: get($type) . '</h4>';

            $html_events = array();

            foreach ($events as $index => $event)
            {
                $event_renderer = StartEndDateEventRenderer :: factory($this, $event, $from_time, $to_time);
                $html_events[$event->get_start_date()][] = $event_renderer->run();
            }

            ksort($html_events);

            foreach ($html_events as $time => $content)
            {
                $output[] = implode(PHP_EOL, $content);
            }
        }

        return implode('', $output);
    }
}
