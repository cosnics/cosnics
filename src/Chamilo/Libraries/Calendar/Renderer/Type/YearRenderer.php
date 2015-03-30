<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Calendar\Renderer\Event\StartDateEventRenderer;
use Chamilo\Libraries\Calendar\Renderer\Type\TableRenderer;
use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Calendar\Table\Type\YearCalendar;
use Chamilo\Libraries\Format\Theme;

/**
 *
 * @package application\personal_calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class YearRenderer extends TableRenderer
{

    /**
     *
     * @return \libraries\calendar\table\YearCalendar
     */
    public function initialize_calendar()
    {
        return new YearCalendar($this->get_time());
    }

    /**
     *
     * @see \application\personal_calendar\Renderer::render()
     */
    public function render()
    {
        $calendar = $this->get_calendar();

        $start_time = $calendar->get_start_time();
        $end_time = $calendar->get_end_time();

        $events = $this->get_events($this, $start_time, $end_time);

        $table_date = $start_time;

        while ($table_date <= $end_time)
        {
            $next_table_date = strtotime('+1 Day', $table_date);

            foreach ($events as $index => $event)
            {
                $start_date = $event->get_start_date();
                $end_date = $event->get_end_date();

                $visible = $table_date < $start_date && $start_date < $next_table_date ||
                     $table_date < $end_date && $end_date <= $next_table_date ||
                     $start_date <= $table_date && $next_table_date <= $end_date;

                if ($visible)
                {
                    if (! $calendar->contains_events_for_time($table_date))
                    {
                        $marker = '<br /><div class="event_marker" style="width: 14px; height: 15px;"><img src="' .
                             Theme :: getInstance()->getCommonImagePath('Action/Marker') . '"/></div>';
                        $calendar->add_event($table_date, $marker);
                    }

                    $event_renderer = StartDateEventRenderer :: factory($this, $event, $table_date);
                    $calendar->add_event($table_date, $event_renderer->run());
                }
            }
            $table_date = $next_table_date;
        }

        $calendar->add_calendar_navigation(
            $this->get_application()->get_url(array(self :: PARAM_TIME => Calendar :: TIME_PLACEHOLDER)));

        $html = array();
        $html[] = $calendar->render();
        $html[] = $this->build_legend();
        return implode(PHP_EOL, $html);
    }
}
