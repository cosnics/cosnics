<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

use Chamilo\Libraries\Calendar\Renderer\Event\EventRendererFactory;
use Chamilo\Libraries\Calendar\Renderer\Type\ViewRenderer;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ListRenderer extends ViewRenderer
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
        $upcomingEvents = $this->getEvents(time(), strtotime('+6 Months', time()));
        $html[] = $this->renderEvents($upcomingEvents, 'UpcomingEvents');

        // Recent events: range from one months ago until now
        $recentEvents = $this->getEvents(strtotime('-2 Months', time()), time());
        $html[] = $this->renderEvents($recentEvents, 'RecentEvents');

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

            $htmlEvents = array();

            foreach ($events as $index => $event)
            {
                $eventRendererFactory = new EventRendererFactory($this, $event);
                $htmlEvents[$event->getStartDate()][] = $eventRendererFactory->render();
            }

            ksort($htmlEvents);

            foreach ($htmlEvents as $time => $content)
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
