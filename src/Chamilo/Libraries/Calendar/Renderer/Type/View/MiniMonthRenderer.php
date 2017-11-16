<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

use Chamilo\Libraries\Calendar\Renderer\Event\EventRendererFactory;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Renderer\Legend;
use Chamilo\Libraries\Calendar\Renderer\Type\ViewRenderer;
use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Calendar\Table\Type\MiniMonthCalendar;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniMonthRenderer extends ViewRenderer
{
    use \Chamilo\Libraries\Calendar\Renderer\Type\View\TableRenderer;

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
     * @param integer $displayTime
     * @param string $linkTarget
     * @param integer $markPeriod
     */
    public function __construct(CalendarRendererProviderInterface $dataProvider, Legend $legend, $displayTime,
        $viewActions = array(), $linkTarget = '', $markPeriod = null)
    {
        $this->markPeriod = $markPeriod;

        parent::__construct($dataProvider, $legend, $displayTime, $viewActions, $linkTarget);
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
        $html = array();

        $html[] = '<div class="panel panel-default">';
        $html[] = $this->renderNavigation();

        $html[] = '<div class="table-calendar-mini-container">';
        $html[] = $this->renderCalendar();
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderCalendar()
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
                    $this->getLegend()->addSource($event->getSource());

                    $configuration = new \Chamilo\Libraries\Calendar\Renderer\Event\Configuration();
                    $configuration->setStartDate($tableDate);

                    $eventRendererFactory = new EventRendererFactory($this, $event, $configuration);
                    $calendar->addEvent($tableDate, $eventRendererFactory->render());
                }
            }

            $tableDate = $nextTableDate;
        }

        $calendar->addNavigationLinks($this->determineNavigationUrl());

        $html = array();

        $html[] = '<div class="table-calendar-mini-container">';
        $html[] = $calendar->render();
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';

        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries\Calendar\Renderer', true) . 'EventTooltip.js');

        return implode(PHP_EOL, $html);
    }

    /**
     * Adds a navigation bar to the calendar
     *
     * @return string
     */
    public function renderNavigation()
    {
        $html = array();

        $html[] = '<div class="panel-heading table-calendar-mini-navigation">';
        $html[] = $this->renderPreviousMonthNavigation();
        $html[] = $this->renderNextMonthNavigation();
        $html[] = '<h4 class="panel-title">';
        $html[] = $this->renderTitle();
        $html[] = '</h4>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderTitle()
    {
        return Translation::get(date('F', $this->getDisplayTime()) . 'Long', null, Utilities::COMMON_LIBRARIES) . ' ' .
             date('Y', $this->getDisplayTime());
    }

    /**
     *
     * @return string
     */
    public function renderPreviousMonthNavigation()
    {
        $urlFormat = $this->determineNavigationUrl();
        $previousTime = strtotime('-1 Month', $this->getDisplayTime());
        $previousUrl = str_replace(Calendar::TIME_PLACEHOLDER, $previousTime, $urlFormat);

        return '<a href="' . $previousUrl . '"><span class="glyphicon glyphicon-chevron-left pull-left"></span></a>';
    }

    /**
     *
     * @return string
     */
    public function renderNextMonthNavigation()
    {
        $urlFormat = $this->determineNavigationUrl();
        $nextTime = strtotime('+1 Month', $this->getDisplayTime());
        $nextUrl = str_replace(Calendar::TIME_PLACEHOLDER, $nextTime, $urlFormat);

        return '<a href="' . $nextUrl . '"><span class="glyphicon glyphicon-chevron-right pull-right"></span></a>';
    }
}
