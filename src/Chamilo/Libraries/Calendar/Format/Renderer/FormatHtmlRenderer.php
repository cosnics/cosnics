<?php
namespace Chamilo\Libraries\Calendar\Format\Renderer;

use Chamilo\Libraries\Calendar\CalendarSources;
use Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Event\EventSource;

/**
 *
 * @package Chamilo\Libraries\Calendar\Format\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class FormatHtmlRenderer extends FormatRenderer
{
    // Parameters
    const PARAM_TIME = 'time';
    const PARAM_TYPE = 'type';

    // Markers
    const MARKER_TYPE = '__TYPE__';

    // Types
    const TYPE_DAY = 'Day';
    const TYPE_LIST = 'List';
    const TYPE_MINI_DAY = 'MiniDay';
    const TYPE_MINI_MONTH = 'MiniMonth';
    const TYPE_MONTH = 'Month';
    const TYPE_WEEK = 'Week';
    const TYPE_YEAR = 'Year';

    /**
     *
     * @var \Chamilo\Libraries\Calendar\CalendarSources
     */
    private $calendarSources;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface $dataProvider
     * @param \Chamilo\Libraries\Calendar\CalendarSources $calendarSources
     * @param integer $displayTime
     */
    public function __construct(CalendarRendererProviderInterface $dataProvider, CalendarSources $calendarSources)
    {
        parent::__construct($dataProvider);

        $this->calendarSources = $calendarSources;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\CalendarSources
     */
    public function getCalendarSources()
    {
        return $this->calendarSources;
    }

    /**
     * Check whether the given source is visible for the user
     *
     * @param \Chamilo\Libraries\Calendar\Event\EventSource $source
     * @return boolean
     */
    public function isSourceVisible(EventSource $source)
    {
        if ($this->getDataProvider()->supportsVisibility())
        {
            return $this->getDataProvider()->isSourceVisible($source);
        }

        return true;
    }

    /**
     * Get the events between $start_time and $end_time which should be displayed in the calendar
     *
     * @param integer $startTime
     * @param integer $endTime
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getEvents($startTime, $endTime)
    {
        $events = $this->getDataProvider()->getAllEventsInPeriod($startTime, $endTime);

        usort(
            $events,
            function ($eventLeft, $eventRight)
            {
                if ($eventLeft->getStartDate() < $eventRight->getStartDate())
                {
                    return - 1;
                }
                elseif ($eventLeft->getStartDate() > $eventRight->getStartDate())
                {
                    return 1;
                }
                else
                {
                    return 0;
                }
            });

        return $events;
    }

    /**
     *
     * @return integer
     */
    abstract public function getStartTime();

    /**
     *
     * @return integer
     */
    abstract public function getEndTime();
}
