<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Architecture\Traits\TableRendererTrait;
use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Service\Event\Configuration;
use Chamilo\Libraries\Calendar\Service\Event\EventMiniMonthRenderer;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Calendar\Service\View\Table\CalendarTable;
use Chamilo\Libraries\Calendar\Service\View\Table\MiniMonthCalendarTable;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniMonthCalendarRenderer extends HtmlCalendarRenderer
{
    use TableRendererTrait;

    protected EventMiniMonthRenderer $eventMiniMonthRenderer;

    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        EventMiniMonthRenderer $eventMiniMonthRenderer
    )
    {
        parent::__construct($legendRenderer, $urlGenerator, $translator);

        $this->eventMiniMonthRenderer = $eventMiniMonthRenderer;
    }

    /**
     * @throws \Exception
     */
    public function render(CalendarRendererProviderInterface $dataProvider, int $displayTime, array $viewActions = []
    ): string
    {
        $html = [];

        $html[] = '<div class="panel panel-default">';
        $html[] = $this->renderNavigation($dataProvider, $displayTime);

        $html[] = '<div class="table-calendar-mini-container">';
        $html[] = $this->renderCalendar($dataProvider, $displayTime);
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getEventMiniMonthRenderer(): EventMiniMonthRenderer
    {
        return $this->eventMiniMonthRenderer;
    }

    /**
     * @throws \ReflectionException
     */
    public function initializeCalendar(CalendarRendererProviderInterface $dataProvider, int $displayTime): CalendarTable
    {
        return new MiniMonthCalendarTable($displayTime, $this->determineNavigationUrl($dataProvider));
    }

    public function isFadedEvent(int $displayTime, Event $event): bool
    {
        $startDate = $event->getStartDate();

        $fromDate = strtotime(date('Y-m-1', $displayTime));
        $toDate = strtotime('-1 Second', strtotime('Next Month', $fromDate));

        return $startDate < $fromDate || $startDate > $toDate;
    }

    /**
     * @throws \Exception
     */
    public function renderCalendar(CalendarRendererProviderInterface $dataProvider, int $displayTime): string
    {
        $calendar = $this->getCalendar($dataProvider, $displayTime);

        $startTime = $calendar->getStartTime();
        $endTime = $calendar->getEndTime();

        $events = $this->getEvents($dataProvider, $startTime, $endTime);
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
                    $this->getLegendRenderer()->addSource($event->getSource());

                    $configuration = new Configuration();
                    $configuration->setStartDate($tableDate);

                    $calendar->addEvent(
                        $tableDate, $this->getEventMiniMonthRenderer()->render(
                        $event, $tableDate, $nextTableDate, $this->isFadedEvent($displayTime, $event),
                        $this->isEventSourceVisible($dataProvider, $event)
                    )
                    );
                }
            }

            $tableDate = $nextTableDate;
        }

        //$calendar->addNavigationLinks($this->determineNavigationUrl($dataProvider));

        $html = [];

        $html[] = '<div class="table-calendar-mini-container">';
        $html[] = $calendar->render();
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';

        $html[] = ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries\Calendar', true) . 'EventTooltip.js'
        );

        return implode(PHP_EOL, $html);
    }

    public function renderNavigation(CalendarRendererProviderInterface $dataProvider, int $displayTime): string
    {
        $html = [];

        $html[] = '<div class="panel-heading table-calendar-mini-navigation">';
        $html[] = $this->renderPreviousMonthNavigation($dataProvider, $displayTime);
        $html[] = $this->renderNextMonthNavigation($dataProvider, $displayTime);
        $html[] = '<h4 class="panel-title">';
        $html[] = $this->renderTitle($displayTime);
        $html[] = '</h4>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderNextMonthNavigation(CalendarRendererProviderInterface $dataProvider, int $displayTime): string
    {
        $urlFormat = $this->determineNavigationUrl($dataProvider);
        $nextTime = strtotime('+1 Month', $displayTime);
        $nextUrl = str_replace(CalendarTable::TIME_PLACEHOLDER, $nextTime, $urlFormat);

        $glyph = new FontAwesomeGlyph('chevron-right', ['pull-right'], null, 'fas');

        return '<a href="' . $nextUrl . '">' . $glyph->render() . '</a>';
    }

    public function renderPreviousMonthNavigation(CalendarRendererProviderInterface $dataProvider, int $displayTime
    ): string
    {
        $urlFormat = $this->determineNavigationUrl($dataProvider);
        $previousTime = strtotime('-1 Month', $displayTime);
        $previousUrl = str_replace(CalendarTable::TIME_PLACEHOLDER, $previousTime, $urlFormat);

        $glyph = new FontAwesomeGlyph('chevron-left', ['pull-left'], null, 'fas');

        return '<a href="' . $previousUrl . '">' . $glyph->render() . '</a>';
    }

    public function renderTitle(int $displayTime): string
    {
        return $this->getTranslator()->trans(date('F', $displayTime) . 'Long', [], StringUtilities::LIBRARIES) . ' ' .
            date('Y', $displayTime);
    }
}
