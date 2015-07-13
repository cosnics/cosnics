<?php
namespace Chamilo\Libraries\Calendar\Renderer\Event\Type;

use Chamilo\Libraries\Calendar\Renderer\Event\StartEndDateEventRenderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Event\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventMiniListRenderer extends StartEndDateEventRenderer
{

    /**
     * Gets a html representation of an event for a list renderer
     *
     * @return string
     */
    public function run()
    {
        $start_date = $this->get_event()->get_start_date();
        $end_date = $this->get_event()->get_end_date();

        $html[] = '<div class="' . $this->getEventClasses() . '">';
        $html[] = '<div class="' . $this->get_renderer()->get_color_classes($this->get_event()->get_source()) . '">';

        if ($start_date >= $this->get_start_date() && $start_date <= $this->get_end_date() &&
             $start_date != $this->get_start_date())
        {
            $html[] = date('H:i', $start_date);
        }
        elseif ($start_date < $this->get_start_date())
        {
            $html[] = '&larr;';
        }

        $html[] = '<a href="' . $this->get_event()->get_url() . '">';
        $html[] = htmlspecialchars($this->get_event()->get_title());
        $html[] = '</a>';

        if ($start_date != $end_date && $end_date < $this->get_end_date() && $start_date < $this->get_start_date())
        {
            $html[] = date('H:i', $end_date);
        }
        elseif ($start_date != $end_date && $end_date > $this->get_end_date())
        {
            $html[] = '&rarr;';
        }

        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
