<?php
namespace Chamilo\Libraries\Calendar\Renderer\Event\Type;

use Chamilo\Libraries\Calendar\Renderer\Event\HourStepEventRenderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Event\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventMiniDayRenderer extends HourStepEventRenderer
{

    /**
     * Gets a html representation of a calendar event
     *
     * @return string
     */
    public function run()
    {
        $table_end_date = strtotime('+' . $this->get_hour_step() . ' Hours', $this->get_start_date());
        $start_date = $this->get_event()->get_start_date();
        $end_date = $this->get_event()->get_end_date();

        $event_classes = 'event';

        if (! $this->get_renderer()->isSourceVisible($this->get_event()->get_source()))
        {
            $event_classes .= ' event-hidden';
        }

        $html[] = '<div class="' . $event_classes . '">';
        $html[] = '<div class="' . $this->get_renderer()->getLegend()->getSourceClasses($this->get_event()->get_source()) . '">';

        if ($start_date >= $this->get_start_date() && $start_date <= $table_end_date &&
             ($start_date != $this->get_start_date() || $end_date < $table_end_date))
        {
            $html[] = date('H:i', $start_date);
        }
        elseif ($start_date < $this->get_start_date())
        {

            $html[] = '&uarr;';
        }

        $target = $this->get_renderer()->get_link_target();
        $target = $target ? ' target="' . $target . '" ' : '';

        $html[] = '<a href="' . $this->get_event()->get_url() . '"' . $target . '>';
        $html[] = htmlspecialchars($this->get_event()->get_title());
        $html[] = '</a>';

        if ($start_date != $end_date)
        {
            if ($end_date < $table_end_date && $start_date < $this->get_start_date())
            {
                $html[] = date('H:i', $end_date);
            }
            elseif ($end_date > $table_end_date)
            {
                $html[] = '&darr;';
            }
        }

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
