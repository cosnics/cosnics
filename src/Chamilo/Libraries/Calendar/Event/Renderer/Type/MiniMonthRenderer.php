<?php
namespace Chamilo\Libraries\Calendar\Event\Renderer\Type;

use Chamilo\Libraries\Calendar\Event\Event;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniMonthRenderer extends MonthRenderer
{

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Event\Renderer\HtmlTableRenderer::render()
     */
    public function render(Event $event, $startDate)
    {
        $html = array();

        $sourceClasses = $this->getCalendarSources()->getSourceClasses($event->getSource()->getTitle());
        $eventClasses = implode(' ', array('event-container', $sourceClasses));

        $html[] = '<div class="tooltip-event-container">';
        $html[] = '<span class="' . $this->determineEventClasses($event) . '"></span>';
        $html[] = '<span class="tooltip-event-content">' . $this->renderFullTitle($event, $startDate) . '</span>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Event\Renderer\HtmlTableRenderer::renderFullTitle()
     */
    public function renderFullTitle(Event $event, $startDate)
    {
        $fullTitle = '';

        $prefix = $this->renderPrefix($event, $startDate);
        if ($prefix)
        {
            $fullTitle .= '<span class="tooltip-event-prefix">' . $prefix . '</span> ';
        }

        $fullTitle .= htmlentities($event->getTitle());

        $postfix = $this->renderPostfix($event, $startDate);
        if ($postfix)
        {
            $fullTitle .= '<span class="tooltip-event-postfix"> ' . $postfix . '</span>';
        }

        return $fullTitle;
    }
}