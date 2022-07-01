<?php
namespace Chamilo\Libraries\Calendar\Service\Event;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Event\Type
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EventMiniMonthRenderer extends EventMonthRenderer
{

    /**
     * Gets an html representation of an event for the renderer
     *
     * @return string
     * @throws \Exception
     */
    public function render()
    {
        $html = [];

        $event = $this->getEvent();
        $legend = $this->getRenderer()->getLegend();

        $sourceClasses = $legend->getSourceClasses($event->getSource());
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
     *
     * @return string
     */
    public function renderTime($date)
    {
        return date('H:i', $date);
    }
}