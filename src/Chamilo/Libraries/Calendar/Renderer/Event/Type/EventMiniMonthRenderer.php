<?php
namespace Chamilo\Libraries\Calendar\Renderer\Event\Type;

/**
 * Renders the events for the mini month calendar
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EventMiniMonthRenderer extends EventMonthRenderer
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
        $legend = $this->getRenderer()->getLegend();
        
        $sourceClasses = $legend->getSourceClasses($event->getSource());
        $eventClasses = implode(' ', array('event-container', $sourceClasses));
        
        $html[] = '<div class="tooltip-event-container">';
        $html[] = '<span class="' . $eventClasses . '"></span>';
        $html[] = '<span class="tooltip-event-content">' . $this->renderFullTitle() . '</span>';
        $html[] = '</div>';
        
        // echo '<div class="tooltip tooltip-calendar fade top in" role="tooltip" style="top: -31px left: 70px;
        // display:block;"><div class="tooltip-arrow"></div><div class="tooltip-inner">' .
        // implode(PHP_EOL, $html) . '</div></div>';
        
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