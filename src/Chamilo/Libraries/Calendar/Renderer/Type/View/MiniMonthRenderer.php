<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

use Chamilo\Libraries\Calendar\Renderer\Event\EventRendererFactory;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Renderer\Legend;
use Chamilo\Libraries\Calendar\Renderer\Type\View\TableRenderer;
use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Calendar\Table\Type\MiniMonthCalendar;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View\Table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class MiniMonthRenderer extends TableRenderer
{

    /**
     * One of 3 possible values (or null): MiniMonthCalendar :: PERIOD_MONTH, MiniMonthCalendar :: PERIOD_WEEK,
     * MiniMonthCalendar :: PERIOD_DAY;
     *
     * @var integer
     */
    private $markPeriod;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface $dataProvider
     * @param int $displayTime
     * @param string $linkTarget
     * @param int $markPeriod
     */
    public function __construct(CalendarRendererProviderInterface $dataProvider, Legend $legend, $displayTime, $viewActions = array(),
        $linkTarget = '', $markPeriod = null)
    {
        $this->markPeriod = $markPeriod;

        parent :: __construct($dataProvider, $legend, $displayTime, $actions, $linkTarget);
    }

    /**
     *
     * @return integer
     */
    public function getMarkPeriod()
    {
        return $this->markPeriod;
    }

    /**
     *
     * @param integer $markPeriod
     */
    public function setMarkPeriod($markPeriod)
    {
        $this->markPeriod = $markPeriod;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Table\Type\MiniMonthCalendar
     */
    public function initializeCalendar()
    {
        return new MiniMonthCalendar($this->getDisplayTime());
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Renderer::render()
     */
    public function render()
    {
        $calendar = $this->getCalendar();

        $startTime = $calendar->getStartTime();
        $endTime = $calendar->getEndTime();

        $events = $this->getEvents($startTime, $endTime);
        $tableDate = $startTime;

        while ($tableDate <= $endTime)
        {
            $nextTableDate = strtotime('+1 Day', $tableDate);

            foreach ($events as $index => $event)
            {
                $startDate = $event->getStartDate();
                $endDate = $event->getEndDate();

                if ($tableDate < $startDate && $startDate < $nextTableDate ||
                     $tableDate < $endDate && $endDate <= $nextTableDate ||
                     $startDate <= $tableDate && $nextTableDate <= $endDate)
                {
                    if (! $calendar->containsEventsForTime($tableDate))
                    {
                        $marker = '<br /><div class="event_marker" style="width: 14px; height: 15px;"><img src="' . htmlspecialchars(
                            Theme :: getInstance()->getCommonImagePath('Action/Marker')) . '"/></div>';
                        $calendar->addEvent($tableDate, $marker);
                    }

                    $configuration = new \Chamilo\Libraries\Calendar\Renderer\Event\Configuration();
                    $configuration->setStartDate($tableDate);

                    $eventRendererFactory = new EventRendererFactory($this, $event, $configuration);

                    $calendar->addEvent($tableDate, $eventRendererFactory->render());
                }
            }

            $tableDate = $nextTableDate;
        }

        $html = array();

        $html[] = '<div class="panel panel-default">';
        $html[] = $this->renderNavigation();
        $calendar->addNavigationLinks($this->determineNavigationUrl());

        $html[] = '<div class="table-calendar-mini-container">';
        $html[] = $calendar->render();
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Adds a navigation bar to the calendar
     *
     * @param string $urlFormat The *TIME* in this string will be replaced by a timestamp
     */
    public function renderNavigation()
    {
        $urlFormat = $this->determineNavigationUrl();
        $previousTime = strtotime('-1 Month', $this->getDisplayTime());
        $nextTime = strtotime('+1 Month', $this->getDisplayTime());

        $todayUrl = str_replace(Calendar :: TIME_PLACEHOLDER, time(), $urlFormat);
        $previousUrl = str_replace(Calendar :: TIME_PLACEHOLDER, $previousTime, $urlFormat);
        $nextUrl = str_replace(Calendar :: TIME_PLACEHOLDER, $nextTime, $urlFormat);

        $html = array();

        $html[] = '<div class="panel-heading table-calendar-mini-navigation">';
        $html[] = '<a href="' . $previousUrl . '"><span class="glyphicon glyphicon-chevron-left pull-left"></span></a>';
        $html[] = '<a href="' . $nextUrl . '"><span class="glyphicon glyphicon-chevron-right pull-right"></span></a>';
        $html[] = '<h4 class="panel-title">';
        $html[] = Translation :: get(date('F', $this->getDisplayTime()) . 'Long', null, Utilities :: COMMON_LIBRARIES) .
             ' ' . date('Y', $this->getDisplayTime());
        $html[] = '</h4>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
