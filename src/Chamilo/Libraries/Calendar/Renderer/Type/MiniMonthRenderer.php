<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Calendar\Renderer\Event\StartDateEventRenderer;
use Chamilo\Libraries\Calendar\Renderer\Type\TableRenderer;
use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Calendar\Table\Type\MiniMonthCalendar;
use Chamilo\Libraries\Format\Theme;

/**
 *
 * @package application\personal_calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniMonthRenderer extends TableRenderer
{

    /**
     * One of 3 possible values (or null): MiniMonthCalendar :: PERIOD_MONTH, MiniMonthCalendar :: PERIOD_WEEK,
     * MiniMonthCalendar :: PERIOD_DAY;
     *
     * @var int
     */
    private $mark_period;

    /**
     *
     * @param Application $application
     * @param int $display_time
     * @param string $link_target
     * @param int $mark_period
     */
    public function __construct(Application $application, $display_time, $link_target = '', $mark_period = null)
    {
        $this->mark_period = $mark_period;

        parent :: __construct($application, $display_time, $link_target);
    }

    /**
     *
     * @return int
     */
    public function get_mark_period()
    {
        return $this->mark_period;
    }

    /**
     *
     * @param int $mark_period
     */
    public function set_mark_period($mark_period)
    {
        $this->mark_period = $mark_period;
    }

    /**
     *
     * @return \libraries\calendar\table\MiniMonthCalendar
     */
    public function initialize_calendar()
    {
        return new MiniMonthCalendar($this->get_time());
    }

    /**
     *
     * @see \libraries\calendar\renderer\Renderer::render()
     */
    public function render()
    {
        $calendar = $this->get_calendar();

        $html = array();

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

                if ($table_date < $start_date && $start_date < $next_table_date ||
                     $table_date < $end_date && $end_date <= $next_table_date ||
                     $start_date <= $table_date && $next_table_date <= $end_date)
                {
                    if (! $calendar->contains_events_for_time($table_date))
                    {
                        $marker = '<br /><div class="event_marker" style="width: 14px; height: 15px;"><img src="' . htmlspecialchars(
                            Theme :: getInstance()->getCommonImagePath()) . 'action_marker.png"/></div>';
                        $calendar->add_event($table_date, $marker);
                    }

                    $event_renderer = StartDateEventRenderer :: factory($this, $event, $table_date);
                    $calendar->add_event($table_date, $event_renderer->run());
                }
            }
            $table_date = $next_table_date;
        }

        $parameters = array(self :: PARAM_TIME => Calendar :: TIME_PLACEHOLDER);
        $calendar->add_calendar_navigation($this->get_application()->get_url($parameters));

        if (! is_null($this->get_mark_period()))
        {
            $calendar->mark_period($this->get_mark_period());
        }

        $calendar->add_navigation_links($this->get_application()->get_url($parameters));
        $html[] = $calendar->render();
        return implode("\n", $html);
    }
}
