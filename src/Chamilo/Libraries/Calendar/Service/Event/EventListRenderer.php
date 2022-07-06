<?php
namespace Chamilo\Libraries\Calendar\Service\Event;

use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service\Event
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EventListRenderer extends EventRenderer
{
    protected DatetimeUtilities $datetimeUtilities;

    protected Translator $translator;

    public function __construct(
        LegendRenderer $legendRenderer, Translator $translator, DatetimeUtilities $datetimeUtilities
    )
    {
        parent::__construct($legendRenderer);

        $this->translator = $translator;
        $this->datetimeUtilities = $datetimeUtilities;
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem[] $eventActions
     *
     * @throws \Exception
     */
    public function render(Event $event, bool $isEventSourceVisible = true, array $eventActions = []): string
    {
        $legend = $this->getLegendRenderer();

        $sourceClasses = $legend->getSourceClasses($event->getSource());
        $eventClasses = implode(' ', ['event-container', $sourceClasses]);

        $html = [];

        if (!$isEventSourceVisible)
        {
            $rowClasses = ' event-container-hidden';
        }
        else
        {
            $rowClasses = '';
        }

        $html[] =
            '<div class="row' . $rowClasses . '" data-source-key="' . $legend->addSource($event->getSource()) . '">';

        $html[] = '<div class="col-xs-1">';
        $html[] = '<span class="' . $eventClasses . '"></span>';
        $html[] = '</div>';

        $html[] = '<div class="col-xs-3 list-event-item-time">';
        $html[] = $this->getRange($event);
        $html[] = '</div>';

        $html[] = '<div class="col-xs-7 list-event-item-data">';

        if ($event->getUrl())
        {
            $html[] = '<a href="' . $event->getUrl() . '">';
        }

        $html[] = htmlspecialchars($event->getTitle());

        if ($event->getUrl())
        {
            $html[] = '</a>';
        }

        $html[] = '</div>';

        $html[] = '<div class="col-xs-1 list-event-item-actions">';
        $html[] = $this->renderActions($eventActions);
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getRange(Event $event): string
    {
        $translator = $this->getTranslator();
        $datetimeUtilities = $this->getDatetimeUtilities();

        $html = [];

        $dateFormat = $translator->trans('DateTimeFormatLong', [], StringUtilities::LIBRARIES);

        if ($event->getEndDate() != '')
        {
            if (date('Y m d', $event->getStartDate()) == date('Y m d', $event->getEndDate()))
            {
                $dateFormat = $translator->trans('TimeNoSecFormat', [], StringUtilities::LIBRARIES);
            }

            $html[] = '<div class="calendar-event-range">' . htmlentities(
                    $datetimeUtilities->formatLocaleDate($dateFormat, $event->getStartDate()) . ' - ' .
                    $datetimeUtilities->formatLocaleDate($dateFormat, $event->getEndDate())
                ) . '</div>';
        }
        else
        {
            $html[] = '<div class="calendar-event-range">' . $datetimeUtilities->formatLocaleDate(
                    $dateFormat, $event->getStartDate()
                ) . '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function renderActions(array $eventActions = []): string
    {
        $html = [];

        if (count($eventActions))
        {
            $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

            foreach ($eventActions as $action)
            {
                $toolbar->add_item($action);
            }

            $html[] = '<div style="float: right; margin-top: 2px;">';
            $html[] = $toolbar->render();
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }
}
