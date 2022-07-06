<?php
namespace Chamilo\Libraries\Calendar\Service\Event;

use Chamilo\Libraries\Calendar\Event\Event;

/**
 * @package Chamilo\Libraries\Calendar\Service\Event
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EventMiniMonthRenderer extends EventMonthRenderer
{
    public function render(
        Event $event, int $cellStartDate, int $cellEndDate, bool $isEventSourceVisible = true,
        bool $isFadedEvent = false
    ): string
    {


        $eventClasses = $this->determineEventClasses($event, $isFadedEvent, $isEventSourceVisible);

        $title = $this->renderFullTitle($event, $cellStartDate, $cellEndDate);

        $html = [];

        $html[] = '<div class="tooltip-event-container">';
        $html[] = '<span class="' . $eventClasses . '"></span>';
        $html[] = '<span class="tooltip-event-content">' . htmlentities($event->getTitle()) . '</span>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderPostfix(Event $event, int $cellStartDate, int $cellEndDate): string
    {
        $postfix = parent::renderPostfix($event, $cellStartDate, $cellEndDate);

        if ($postfix)
        {
            $postfix = '<span class="tooltip-event-postfix">' . $postfix . '</span> ';
        }

        return $postfix;
    }

    public function renderPrefix(Event $event, int $cellStartDate, int $cellEndDate): string
    {
        $prefix = parent::renderPrefix($event, $cellStartDate, $cellEndDate);

        if ($prefix)
        {
            $prefix = '<span class="tooltip-event-prefix">' . $prefix . '</span> ';
        }

        return $prefix;
    }
}