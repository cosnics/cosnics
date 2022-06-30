<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

use Chamilo\Libraries\Calendar\Renderer\Event\Configuration;
use Chamilo\Libraries\Calendar\Renderer\Event\EventRendererFactory;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Renderer\LegendRenderer;
use Chamilo\Libraries\Calendar\Renderer\Type\ViewRenderer;
use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Calendar\Table\Type\MiniMonthCalendar;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniMonthRenderer extends ViewRenderer
{
    use TableRenderer;

    /**
     * One of 3 possible values (or null): MiniMonthCalendar::PERIOD_MONTH, MiniMonthCalendar::PERIOD_WEEK,
     * MiniMonthCalendar::PERIOD_DAY;
     */
    private ?int $markPeriod;

    public function __construct(
        CalendarRendererProviderInterface $dataProvider, LegendRenderer $legend, int $displayTime,
        array $viewActions = [], string $linkTarget = '', ?int $markPeriod = null
    )
    {
        $this->markPeriod = $markPeriod;

        parent::__construct($dataProvider, $legend, $displayTime, $viewActions, $linkTarget);
    }

    /**
     * @throws \Exception
     */
    public function render(): string
    {
        $html = [];

        $html[] = '<div class="panel panel-default">';
        $html[] = $this->renderNavigation();

        $html[] = '<div class="table-calendar-mini-container">';
        $html[] = $this->renderCalendar();
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getMarkPeriod(): ?int
    {
        return $this->markPeriod;
    }

    public function setMarkPeriod(?int $markPeriod)
    {
        $this->markPeriod = $markPeriod;
    }

    /**
     * @throws \ReflectionException
     */
    public function initializeCalendar(): Calendar
    {
        return new MiniMonthCalendar($this->getDisplayTime());
    }

    /**
     * @throws \Exception
     */
    public function renderCalendar(): string
    {
        $calendar = $this->getCalendar();

        $startTime = $calendar->getStartTime();
        $endTime = $calendar->getEndTime();

        $events = $this->getEvents($startTime, $endTime);
        $tableDate = $startTime;

        while ($tableDate <= $endTime)
        {
            $nextTableDate = strtotime('+1 Day', $tableDate);

            foreach ($events as $event)
            {
                $startDate = $event->getStartDate();
                $endDate = $event->getEndDate();

                if ($tableDate < $startDate && $startDate < $nextTableDate ||
                    $tableDate < $endDate && $endDate <= $nextTableDate ||
                    $startDate <= $tableDate && $nextTableDate <= $endDate)
                {
                    $this->getLegend()->addSource($event->getSource());

                    $configuration = new Configuration();
                    $configuration->setStartDate($tableDate);

                    $eventRendererFactory = new EventRendererFactory($this, $event, $configuration);
                    $calendar->addEvent($tableDate, $eventRendererFactory->render());
                }
            }

            $tableDate = $nextTableDate;
        }

        $calendar->addNavigationLinks($this->determineNavigationUrl());

        $html = [];

        $html[] = '<div class="table-calendar-mini-container">';
        $html[] = $calendar->render();
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';

        $html[] = ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries\Calendar\Renderer', true) . 'EventTooltip.js'
        );

        return implode(PHP_EOL, $html);
    }

    public function renderNavigation(): string
    {
        $html = [];

        $html[] = '<div class="panel-heading table-calendar-mini-navigation">';
        $html[] = $this->renderPreviousMonthNavigation();
        $html[] = $this->renderNextMonthNavigation();
        $html[] = '<h4 class="panel-title">';
        $html[] = $this->renderTitle();
        $html[] = '</h4>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderNextMonthNavigation(): string
    {
        $urlFormat = $this->determineNavigationUrl();
        $nextTime = strtotime('+1 Month', $this->getDisplayTime());
        $nextUrl = str_replace(Calendar::TIME_PLACEHOLDER, $nextTime, $urlFormat);

        $glyph = new FontAwesomeGlyph('chevron-right', ['pull-right'], null, 'fas');

        return '<a href="' . $nextUrl . '">' . $glyph->render() . '</a>';
    }

    public function renderPreviousMonthNavigation(): string
    {
        $urlFormat = $this->determineNavigationUrl();
        $previousTime = strtotime('-1 Month', $this->getDisplayTime());
        $previousUrl = str_replace(Calendar::TIME_PLACEHOLDER, $previousTime, $urlFormat);

        $glyph = new FontAwesomeGlyph('chevron-left', ['pull-left'], null, 'fas');

        return '<a href="' . $previousUrl . '">' . $glyph->render() . '</a>';
    }

    public function renderTitle(): string
    {
        return Translation::get(date('F', $this->getDisplayTime()) . 'Long', null, StringUtilities::LIBRARIES) . ' ' .
            date('Y', $this->getDisplayTime());
    }
}
