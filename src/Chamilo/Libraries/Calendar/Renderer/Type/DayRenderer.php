<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Calendar\Renderer\Event\HourStepEventRenderer;
use Chamilo\Libraries\Calendar\Renderer\Type\TableRenderer;
use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Calendar\Table\Type\DayCalendar;

/**
 *
 * @package application\personal_calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DayRenderer extends TableRenderer
{

    /**
     *
     * @return \libraries\calendar\table\DayCalendar
     */
    public function initialize_calendar()
    {
        return new DayCalendar($this->get_time());
    }

    /**
     *
     * @see \application\personal_calendar\Renderer::render()
     */
    public function render()
    {
        $calendar = $this->get_calendar();
        $from_date = strtotime(date('Y-m-d 00:00:00', $this->get_time()));
        $to_date = strtotime(date('Y-m-d 23:59:59', $this->get_time()));

        $events = $this->get_events($this, $from_date, $to_date);
        $html = array();

        $start_time = $calendar->get_start_time();
        $end_time = $calendar->get_end_time();
        $table_date = $start_time;

        while ($table_date <= $end_time)
        {
            $next_table_date = strtotime('+' . $calendar->get_hour_step() . ' Hours', $table_date);

            foreach ($events as $index => $event)
            {
                $start_date = $event->get_start_date();
                $end_date = $event->get_end_date();

                if ($table_date < $start_date && $start_date < $next_table_date ||
                     $table_date < $end_date && $end_date <= $next_table_date ||
                     $start_date <= $table_date && $next_table_date <= $end_date)
                {
                    $event_renderer = HourStepEventRenderer :: factory(
                        $this,
                        $event,
                        $table_date,
                        $calendar->get_hour_step());
                    $calendar->add_event($table_date, $event_renderer->run());
                }
            }
            $table_date = $next_table_date;
        }

        $calendar->add_calendar_navigation(
            $this->get_application()->get_url(array(self :: PARAM_TIME => Calendar :: TIME_PLACEHOLDER)));

        $html[] = $calendar->render();
        $html[] = $this->build_legend();

        return implode(PHP_EOL, $html);
    }
}
