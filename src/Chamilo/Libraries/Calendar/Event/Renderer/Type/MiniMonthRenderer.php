<?php
namespace Chamilo\Libraries\Calendar\Event\Renderer\Type;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniMonthRenderer extends MonthRenderer
{

    /**
     * Gets an html representation of an event for the renderer
     *
     * @return string
     */
    public function render()
    {
        $html = array();

        $event = $this->getEvent();
        $calendarSources = $this->getRenderer()->getCalendarSources();

        $sourceClasses = $calendarSources->getSourceClasses($event->getSource());
        $eventClasses = implode(' ', array('event-container', $sourceClasses));

        $html[] = '<div class="tooltip-event-container">';
        $html[] = '<span class="' . $eventClasses . '"></span>';
        $html[] = '<span class="tooltip-event-content">' . $this->renderFullTitle() . '</span>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderFullTitle()
    {
        $fullTitle = '';

        $prefix = $this->renderPrefix();
        if ($prefix)
        {
            $fullTitle .= '<span class="tooltip-event-prefix">' . $prefix . '</span> ';
        }

        $fullTitle .= htmlentities($this->getEvent()->getTitle());

        $postfix = $this->renderPostfix();
        if ($postfix)
        {
            $fullTitle .= '<span class="tooltip-event-postfix"> ' . $postfix . '</span>';
        }

        return $fullTitle;
    }

    /**
     *
     * @param integer $date
     * @return string
     */
    public function renderTime($date)
    {
        return date('H:i', $date);
    }
}