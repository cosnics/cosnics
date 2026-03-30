<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Architecture\Domain\CalendarTableConfiguration;
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
    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        ButtonToolBarRenderer $buttonToolBarRenderer, protected EventMiniMonthRenderer $eventMiniMonthRenderer,
        protected MiniMonthCalendarTableBuilder $miniMonthCalendarTableBuilder,
        protected WebPathBuilder $webPathBuilder, protected ResourceManager $resourceManager
    )
    {
        parent::__construct($legendRenderer, $urlGenerator, $translator, $buttonToolBarRenderer);
    }

    /**
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Event[] $events
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Visibility[] $invisibleSources
     *
     * @throws \Exception
     */
    public function render(
        array $events, CalendarTableConfiguration $calendarTableConfiguration, array $displayParameters,
        int $displayTime, array $viewActions = [], array $invisibleSources = [], ?string $invisibilityContext = null
    ): string
    {
        $html = [];

        $html[] = '<div class="panel panel-default">';
        $html[] = $this->renderNavigation($displayParameters, $displayTime);

        $html[] = '<div class="table-calendar-mini-container">';
        $html[] = $this->renderCalendar(
            $calendarTableConfiguration, $events, $displayParameters, $displayTime, $invisibleSources
        );
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getEventsEndTime(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): int
    {
        return $this->miniMonthCalendarTableBuilder->getTableEndTime($calendarTableConfiguration, $displayTime);
    }

    public function getEventsStartTime(CalendarTableConfiguration $calendarTableConfiguration, int $displayTime): int
    {
        return $this->miniMonthCalendarTableBuilder->getTableStartTime($calendarTableConfiguration, $displayTime);
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
        CalendarTableConfiguration $calendarTableConfiguration, array $events, array $displayParameters,
        int $displayTime, array $invisibleSources = []
    ): string
    {
        $startTime = $this->getEventsStartTime($calendarTableConfiguration, $displayTime);
        $endTime = $this->getEventsEndTime($calendarTableConfiguration, $displayTime);

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
                    $this->legendRenderer->addSource($event->getSource());

                    $eventsToShow[$tableDate][] = $this->eventMiniMonthRenderer->render(
                        $event, $tableDate, $nextTableDate, $this->isEventSourceVisible($event, $invisibleSources),
                        $this->isFadedEvent($displayTime, $event)
                    );
                }
            }

            $tableDate = $nextTableDate;
        }

        $html = [];

        $html[] = '<div class="table-calendar-mini-container">';
        $html[] = $this->miniMonthCalendarTableBuilder->render(
            $calendarTableConfiguration, $displayTime, $eventsToShow, ['table-calendar-mini'],
            $this->determineNavigationUrl($displayParameters)
        );
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';

        $html[] = $this->resourceManager->getResourceHtml(
            $this->webPathBuilder->getJavascriptPath() . 'Calendar/EventTooltip.js'
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
        return $this->translator->trans(date('F', $displayTime) . 'Long', [], StringUtilities::LIBRARIES) . ' ' .
            date('Y', $displayTime);
    }
}
