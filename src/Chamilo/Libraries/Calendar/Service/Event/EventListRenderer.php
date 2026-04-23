<?php
namespace Chamilo\Libraries\Calendar\Service\Event;

use Chamilo\Libraries\Calendar\Architecture\Domain\Event;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Service\Utilities\DatetimeUtilities;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\MiniButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\MiniButtonToolBarRenderer;
use IntlDateFormatter;

/**
 * @package Chamilo\Libraries\Calendar\Service\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EventListRenderer extends EventRenderer
{
    public function __construct(
        LegendRenderer $legendRenderer, protected DatetimeUtilities $datetimeUtilities,
        protected MiniButtonToolBarRenderer $miniButtonToolBarRenderer
    )
    {
        parent::__construct($legendRenderer);
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button[] $eventActions
     *
     * @throws \Exception
     */
    public function render(Event $event, bool $isEventSourceVisible = true, array $eventActions = []): string
    {
        $sourceClasses = $this->legendRenderer->getSourceClasses($event->getSource());
        $eventClasses = implode(' ', ['event-container', $sourceClasses]);

        $html = [];

        if (!$isEventSourceVisible) {
            $rowClasses = ' event-container-hidden';
        }
        else {
            $rowClasses = '';
        }

        $html[] = '<div class="row' . $rowClasses . '" data-source-key="' .
            $this->legendRenderer->addSource($event->getSource()) . '">';

        $html[] = '<div class="col-1">';
        $html[] = '<span class="' . $eventClasses . '"></span>';
        $html[] = '</div>';

        $html[] = '<div class="col-3 list-event-item-time">';
        $html[] = $this->getRange($event);
        $html[] = '</div>';

        $html[] = '<div class="col-7 list-event-item-data">';

        if ($event->getUrl()) {
            $html[] = '<a href="' . $event->getUrl() . '">';
        }

        $html[] = htmlspecialchars($event->getTitle());

        if ($event->getUrl()) {
            $html[] = '</a>';
        }

        $html[] = '</div>';

        $html[] = '<div class="col-1 list-event-item-actions">';
        $html[] = $this->renderActions($eventActions);
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getRange(Event $event): string
    {
        $html = [];

        $dateFormat = IntlDateFormatter::SHORT;
        $timeFormat = IntlDateFormatter::SHORT;

        if ($event->getEndDate() != '') {
            if (date('Y m d', $event->getStartDate()) == date('Y m d', $event->getEndDate())) {
                $dateFormat = IntlDateFormatter::NONE;
            }

            $html[] = '<div class="calendar-event-range">' . htmlentities(
                    $this->datetimeUtilities->formatLocaleDate($event->getStartDate(), $dateFormat, $timeFormat) .
                    ' - ' . $this->datetimeUtilities->formatLocaleDate($event->getEndDate(), $dateFormat, $timeFormat)
                ) . '</div>';
        }
        else {
            $html[] = '<div class="calendar-event-range">' . $this->datetimeUtilities->formatLocaleDate(
                    $event->getStartDate(), $dateFormat, $timeFormat
                ) . '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function renderActions(array $eventActions = []): string
    {
        $html = [];

        if (count($eventActions)) {
            $buttonToolBar = new MiniButtonToolBar();

            foreach ($eventActions as $action) {
                $buttonToolBar->addButton($action);
            }

            $html[] = '<div style="float: right; margin-top: 2px;">';
            $html[] = $this->miniButtonToolBarRenderer->render($buttonToolBar);
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }
}
