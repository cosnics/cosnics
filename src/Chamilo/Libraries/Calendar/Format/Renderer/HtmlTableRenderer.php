<?php
namespace Chamilo\Libraries\Calendar\Format\Renderer;

use Chamilo\Libraries\Calendar\CalendarConfiguration;
use Chamilo\Libraries\Calendar\CalendarSources;
use Chamilo\Libraries\Calendar\Format\HtmlTable\Calendar;
use Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface;

/**
 *
 * @package Chamilo\Libraries\Calendar\Format\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class HtmlTableRenderer extends ViewRenderer
{

    /**
     *
     * @var \Chamilo\Libraries\Calendar\CalendarConfiguration
     */
    private $calendarConfiguration;

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Format\HtmlTable\Calendar
     */
    private $calendar;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface $dataProvider
     * @param \Chamilo\Libraries\Calendar\CalendarSources $calendarSources
     * @param \Chamilo\Libraries\Calendar\CalendarConfiguration $calendarConfiguration
     * @param \Chamilo\Libraries\Calendar\Format\HtmlTable\Calendar $calendar
     * @param integer $displayTime
     */
    public function __construct(CalendarRendererProviderInterface $dataProvider, CalendarSources $calendarSources, 
        CalendarConfiguration $calendarConfiguration, Calendar $calendar, $displayTime)
    {
        parent::__construct($dataProvider, $calendarSources, $displayTime);
        
        $this->calendarConfiguration = $calendarConfiguration;
        $this->calendar = $calendar;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\CalendarConfiguration
     */
    protected function getCalendarConfiguration()
    {
        return $this->calendarConfiguration;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Format\HtmlTable\Calendar
     */
    protected function getCalendar()
    {
        return $this->calendar;
    }
}