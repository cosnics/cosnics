<?php
namespace Chamilo\Libraries\Calendar\Renderer\Event\Type;

use Chamilo\Libraries\Calendar\Renderer\Event\StartDateEventRenderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Event\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventMiniMonthRenderer extends StartDateEventRenderer
{

    /**
     * Gets a html representation of an event for a mini month renderer
     *
     * @return string
     */
    public function run()
    {
        $start_date = $this->get_event()->get_start_date();
        $end_date = $this->get_event()->get_end_date();

        $from_date = strtotime(date('Y-m-1', $this->get_renderer()->get_time()));
        $to_date = strtotime('-1 Second', strtotime('Next Month', $from_date));

        $eventClasses = $this->getEventClasses();

        if (($start_date < $from_date || $start_date > $to_date))
        {
            $eventClasses .= ' event_fade';
        }

        $html[] = '<div class="' . $eventClasses . '" style="display: none;">';
        $html[] = '<div class="' . $this->get_renderer()->getLegend()->getSourceClasses(
            $this->get_event()->get_source(),
            (($start_date < $from_date || $start_date > $to_date) ? true : false)) . '">';

        if ($start_date >= $this->get_start_date() && $start_date <= strtotime('+1 Day', $this->get_start_date()) &&
             $start_date != $this->get_start_date())
        {
            $html[] = date('H:i', $start_date);
        }
        elseif ($start_date < $this->get_start_date())
        {
            $html[] = '&larr;';
        }

        $target = $this->get_renderer()->get_link_target();
        $target = $target ? ' target="' . $target . '" ' : '';

        $html[] = '<a href="' . $this->get_event()->get_url() . '"' . $target . '>';
        $html[] = htmlspecialchars($this->get_event()->get_title());
        $html[] = '</a>';

        if ($start_date != $end_date && $end_date < strtotime('+1 Day', $this->get_start_date()) &&
             $start_date < $this->get_start_date())
        {
            $html[] = date('H:i', $end_date);
        }
        elseif ($start_date != $end_date && $end_date > strtotime('+1 Day', $this->get_start_date()))
        {
            $html[] = '&rarr;';
        }

        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
