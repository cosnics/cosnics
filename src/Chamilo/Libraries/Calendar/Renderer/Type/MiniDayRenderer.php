<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Calendar\Renderer\Event\HourStepEventRenderer;
use Chamilo\Libraries\Calendar\Renderer\Type\TableRenderer;
use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Calendar\Table\Type\MiniDayCalendar;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRenderer;

/**
 *
 * @package application\personal_calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniDayRenderer extends TableRenderer
{

    /**
     *
     * @var int
     */
    private $hour_step;

    /**
     *
     * @var int
     */
    private $start_hour;

    /**
     *
     * @var int
     */
    private $end_hour;

    /**
     *
     * @param CalendarRenderer $application
     * @param int $display_time
     * @param int $hour_step
     * @param int $start_hour
     * @param int $end_hour
     * @param string $link_target
     */
    public function __construct(CalendarRenderer $application, $display_time, $hour_step = 1, $start_hour = 0,
        $end_hour = 24, $link_target = '')
    {
        $this->hour_step = $hour_step;
        $this->start_hour = $start_hour;
        $this->end_hour = $end_hour;

        parent :: __construct($application, $display_time, $link_target);
    }

    /**
     *
     * @return int
     */
    public function get_hour_step()
    {
        return $this->hour_step;
    }

    /**
     *
     * @param int $hour_step
     */
    public function set_hour_step($hour_step)
    {
        $this->hour_step = $hour_step;
    }

    /**
     *
     * @return int
     */
    public function get_start_hour()
    {
        return $this->start_hour;
    }

    /**
     *
     * @param int $start_hour
     */
    public function set_start_hour($start_hour)
    {
        $this->start_hour = $start_hour;
    }

    /**
     *
     * @return int
     */
    public function get_end_hour()
    {
        return $this->end_hour;
    }

    /**
     *
     * @param int $end_hour
     */
    public function set_end_hour($end_hour)
    {
        $this->end_hour = $end_hour;
    }

    /**
     *
     * @return \libraries\calendar\table\MiniDayCalendar
     */
    public function initialize_calendar()
    {
        return new MiniDayCalendar(
            $this->get_time(),
            $this->get_hour_step(),
            $this->get_start_hour(),
            $this->get_end_hour());
    }

    /**
     *
     * @see \libraries\calendar\renderer\Renderer::render()
     */
    public function render()
    {
        $calendar = $this->get_calendar();

        $from_date = $calendar->get_start_time();
        $to_date = $calendar->get_end_time();

        $events = $this->get_events($this, $from_date, $to_date);

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
                     $table_date < $end_date && $end_date < $next_table_date ||
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

        $html = array();
        $html[] = $calendar->render();
        $html[] = $this->build_legend();
        return implode(PHP_EOL, $html);
    }
}
