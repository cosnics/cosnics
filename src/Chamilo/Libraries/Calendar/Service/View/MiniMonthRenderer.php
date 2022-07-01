<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Architecture\Traits\TableRendererTrait;
use Chamilo\Libraries\Calendar\Service\Event\Configuration;
use Chamilo\Libraries\Calendar\Service\Event\EventMonthRenderer;
use Chamilo\Libraries\Calendar\Service\View\Table\CalendarTable;
use Chamilo\Libraries\Calendar\Service\View\Table\MiniMonthCalendarTable;
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
class MiniMonthRenderer extends HtmlCalendarRenderer
{
    use TableRendererTrait;

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

    /**
     * @throws \ReflectionException
     */
    public function initializeCalendar(): CalendarTable
    {
        return new MiniMonthCalendarTable($this->getDisplayTime());
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

                    $eventRendererFactory = new EventMonthRenderer($this, $event, $configuration);
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
        $nextUrl = str_replace(CalendarTable::TIME_PLACEHOLDER, $nextTime, $urlFormat);

        $glyph = new FontAwesomeGlyph('chevron-right', ['pull-right'], null, 'fas');

        return '<a href="' . $nextUrl . '">' . $glyph->render() . '</a>';
    }

    public function renderPreviousMonthNavigation(): string
    {
        $urlFormat = $this->determineNavigationUrl();
        $previousTime = strtotime('-1 Month', $this->getDisplayTime());
        $previousUrl = str_replace(CalendarTable::TIME_PLACEHOLDER, $previousTime, $urlFormat);

        $glyph = new FontAwesomeGlyph('chevron-left', ['pull-left'], null, 'fas');

        return '<a href="' . $previousUrl . '">' . $glyph->render() . '</a>';
    }

    public function renderTitle(): string
    {
        return Translation::get(date('F', $this->getDisplayTime()) . 'Long', null, StringUtilities::LIBRARIES) . ' ' .
            date('Y', $this->getDisplayTime());
    }
}
