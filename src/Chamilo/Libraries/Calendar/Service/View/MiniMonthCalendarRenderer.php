<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Architecture\Domain\Event;
use Chamilo\Libraries\Calendar\Service\Event\EventMiniMonthRenderer;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Calendar\Service\TableBuilder\CalendarTableBuilder;
use Chamilo\Libraries\Calendar\Service\TableBuilder\MiniMonthCalendarTableBuilder;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Resource\ResourceManager;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service\View
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniMonthCalendarRenderer extends MiniCalendarRenderer
{
    protected EventMiniMonthRenderer $eventMiniMonthRenderer;

    protected MiniMonthCalendarTableBuilder $miniMonthCalendarTableBuilder;

    protected ResourceManager $resourceManager;

    protected WebPathBuilder $webPathBuilder;

    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        EventMiniMonthRenderer $eventMiniMonthRenderer, MiniMonthCalendarTableBuilder $miniMonthCalendarTableBuilder,
        WebPathBuilder $webPathBuilder, ResourceManager $resourceManager, ButtonToolBarRenderer $buttonToolBarRenderer
    )
    {
        parent::__construct($legendRenderer, $urlGenerator, $translator, $buttonToolBarRenderer);

        $this->eventMiniMonthRenderer = $eventMiniMonthRenderer;
        $this->miniMonthCalendarTableBuilder = $miniMonthCalendarTableBuilder;
        $this->webPathBuilder = $webPathBuilder;
        $this->resourceManager = $resourceManager;
    }

    /**
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Event[] $events
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Visibility[] $invisibleSources
     *
     * @throws \Exception
     */
    public function render(
        array $events, array $displayParameters, int $displayTime, array $viewActions = [],
        array $invisibleSources = [], ?string $invisibilityContext = null
    ): string
    {
        $html = [];

        $html[] = '<div class="panel panel-default">';
        $html[] = $this->renderNavigation($displayParameters, $displayTime);

        $html[] = '<div class="table-calendar-mini-container">';
        $html[] = $this->renderCalendar($events, $displayParameters, $displayTime, $invisibleSources);
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getEventMiniMonthRenderer(): EventMiniMonthRenderer
    {
        return $this->eventMiniMonthRenderer;
    }

    public function getEventsEndTime(int $displayTime): int
    {
        return $this->getMiniMonthCalendarTableBuilder()->getTableEndTime($displayTime);
    }

    public function getEventsStartTime(int $displayTime): int
    {
        return $this->getMiniMonthCalendarTableBuilder()->getTableStartTime($displayTime);
    }

    public function getMiniMonthCalendarTableBuilder(): MiniMonthCalendarTableBuilder
    {
        return $this->miniMonthCalendarTableBuilder;
    }

    public function getResourceManager(): ResourceManager
    {
        return $this->resourceManager;
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }

    public function isFadedEvent(int $displayTime, Event $event): bool
    {
        $startDate = $event->getStartDate();

        $fromDate = strtotime(date('Y-m-1', $displayTime));
        $toDate = strtotime('-1 Second', strtotime('Next Month', $fromDate));

        return $startDate < $fromDate || $startDate > $toDate;
    }

    /**
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Event[] $events
     *
     * @throws \Exception
     */
    public function renderCalendar(
        array $events, array $displayParameters, int $displayTime, array $invisibleSources = []
    ): string
    {
        $calendarTableBuilder = $this->getMiniMonthCalendarTableBuilder();

        $startTime = $this->getEventsStartTime($displayTime);
        $endTime = $this->getEventsEndTime($displayTime);

        $events = $this->orderEvents($events);
        $tableDate = $startTime;
        $eventsToShow = [];

        while ($tableDate <= $endTime) {
            $nextTableDate = strtotime('+1 Day', $tableDate);

            foreach ($events as $event) {
                $startDate = $event->getStartDate();
                $endDate = $event->getEndDate();

                if ($tableDate < $startDate && $startDate < $nextTableDate ||
                    $tableDate < $endDate && $endDate <= $nextTableDate ||
                    $startDate <= $tableDate && $nextTableDate <= $endDate) {
                    $this->getLegendRenderer()->addSource($event->getSource());

                    $eventsToShow[$tableDate][] = $this->getEventMiniMonthRenderer()->render(
                        $event, $tableDate, $nextTableDate, $this->isEventSourceVisible($event, $invisibleSources),
                        $this->isFadedEvent($displayTime, $event)
                    );
                }
            }

            $tableDate = $nextTableDate;
        }

        $html = [];

        $html[] = '<div class="table-calendar-mini-container">';
        $html[] = $calendarTableBuilder->render($displayTime, $eventsToShow, ['table-calendar-mini'],
            $this->determineNavigationUrl($displayParameters));
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';

        $html[] = $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath() . 'Calendar/EventTooltip.js'
        );

        return implode(PHP_EOL, $html);
    }

    public function renderNavigation(array $displayParameters, int $displayTime): string
    {
        $html = [];

        $html[] = '<div class="panel-heading table-calendar-mini-navigation">';
        $html[] = $this->renderPreviousMonthNavigation($displayParameters, $displayTime);
        $html[] = $this->renderNextMonthNavigation($displayParameters, $displayTime);
        $html[] = '<h4 class="panel-title">';
        $html[] = $this->renderTitle($displayTime);
        $html[] = '</h4>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderNextMonthNavigation(array $displayParameters, int $displayTime): string
    {
        $urlFormat = $this->determineNavigationUrl($displayParameters);
        $nextTime = strtotime('+1 Month', $displayTime);
        $nextUrl = str_replace(CalendarTableBuilder::TIME_PLACEHOLDER, (string) $nextTime, $urlFormat);

        $glyph = new FontAwesomeGlyph('chevron-right', ['float-end'], null, 'fas');

        return '<a href="' . $nextUrl . '">' . $glyph->render() . '</a>';
    }

    public function renderPreviousMonthNavigation(array $displayParameters, int $displayTime): string
    {
        $urlFormat = $this->determineNavigationUrl($displayParameters);
        $previousTime = strtotime('-1 Month', $displayTime);
        $previousUrl = str_replace(CalendarTableBuilder::TIME_PLACEHOLDER, (string) $previousTime, $urlFormat);

        $glyph = new FontAwesomeGlyph('chevron-left', ['float-start'], null, 'fas');

        return '<a href="' . $previousUrl . '">' . $glyph->render() . '</a>';
    }

    public function renderTitle(int $displayTime): string
    {
        return $this->getTranslator()->trans(date('F', $displayTime) . 'Long', [], StringUtilities::LIBRARIES) . ' ' .
            date('Y', $displayTime);
    }
}
