<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Calendar\Renderer\Event\EventRenderer;
use Chamilo\Libraries\Calendar\Renderer\Renderer;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ListRenderer extends Renderer
{

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Renderer::render()
     */
    public function render()
    {
        $html = array();

        $html[] = '<div class="calendar-container">';

        // Upcoming events: range from now until 6 months in the future
        $upcoming_events = $this->get_events($this, time(), strtotime('+6 Months', time()));
        $html[] = $this->renderEvents($upcoming_events, 'UpcomingEvents');

        // Recent events: range from one months ago until now
        $recent_events = $this->get_events($this, strtotime('-2 Months', time()), time());
        $html[] = $this->renderEvents($recent_events, 'RecentEvents');

        $html[] = '</div>';

        $html[] = $this->getLegend()->render();

        return implode(PHP_EOL, $html);
    }

    private function renderEvents($events, $type)
    {
        $output = array();

        $output[] = '<h3>' . Translation :: get($type) . '</h3>';

        if (count($events) > 0)
        {

            $html_events = array();

            foreach ($events as $index => $event)
            {
                $event_renderer = EventRenderer :: factory($this, $event);
                $html_events[$event->get_start_date()][] = $event_renderer->run();
            }

            ksort($html_events);

            foreach ($html_events as $time => $content)
            {
                $output[] = implode(PHP_EOL, $content);
            }
        }
        else
        {
            $output[] = Display :: normal_message(Translation :: get('No' . $type), true);
        }

        return implode('', $output);
    }
}
