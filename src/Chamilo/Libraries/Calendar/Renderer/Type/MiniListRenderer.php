<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Calendar\Renderer\Renderer;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Calendar\Renderer\Event\EventRendererFactory;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class MiniListRenderer extends Renderer
{

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Renderer::render()
     */
    public function render()
    {
        $html = array();

        $html[] = '<div class="calendar-container">';

        // Today's events, from the current time until midnight
        $fromTime = time();
        $toTime = strtotime('tomorrow -1 second', time());
        $events = $this->getEvents($this, $fromTime, $toTime);
        $html[] = $this->renderEvents($events, 'Today', $fromTime, $toTime);

        // Tomorrow's events, from midnight tomorrow until midnight the day after tomorrow
        $fromTime = strtotime('tomorrow', time());
        $toTime = strtotime('tomorrow +1 day -1 second', time());
        $events = $this->getEvents($this, $fromTime, $toTime);
        $html[] = $this->renderEvents($events, 'Tomorrow', $fromTime, $toTime);

        // Events that will happen soon, from midnight the day after tomorrow untill next week midnight
        $fromTime = strtotime('tomorrow +1 day', time());
        $toTime = strtotime('tomorrow +7 days -1 second', time());
        $events = $this->getEvents($this, $fromTime, $toTime);
        $html[] = $this->renderEvents($events, 'Soon', $fromTime, $toTime);

        $html[] = '</div>';

        $html[] = $this->getLegend()->render();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\Event[] $events
     * @param string $type
     * @param integer $fromTime
     * @param integer $toTime
     */
    public function renderEvents($events, $type, $fromTime, $toTime)
    {
        $output = array();

        if (count($events) > 0)
        {
            $output[] = '<h4>' . Translation :: get($type) . '</h4>';

            $htmlEvents = array();

            foreach ($events as $index => $event)
            {
                $configuration = new \Chamilo\Libraries\Calendar\Renderer\Event\Configuration();
                $configuration->setStartDate($fromTime);
                $configuration->setEndDate($toTime);

                $eventRendererFactory = new EventRendererFactory($this, $event, $configuration);

                $htmlEvents[$event->getStartDate()][] = $eventRendererFactory->render();
            }

            ksort($htmlEvents);

            foreach ($htmlEvents as $time => $content)
            {
                $output[] = implode(PHP_EOL, $content);
            }
        }

        return implode('', $output);
    }
}
