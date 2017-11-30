<?php
namespace Chamilo\Libraries\Calendar\Format\Renderer\Type;

use Chamilo\Libraries\Calendar\Event\Service\HtmlTableRendererFactory;
use Chamilo\Libraries\Calendar\Format\Renderer\ViewRenderer;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Libraries\Calendar\Format\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ListRenderer extends ViewRenderer
{

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Format\Renderer\ViewRenderer::getEvents()
     */
    public function getEvents($startTime, $endTime)
    {
        $events = parent::getEvents($startTime, $endTime);
        
        $structuredEvents = array();
        
        foreach ($events as $event)
        {
            $startDate = $event->getStartDate();
            $dateKey = mktime(0, 0, 0, date('n', $startDate), date('j', $startDate), date('Y', $startDate));
            
            if (! isset($structuredEvents[$dateKey]))
            {
                $structuredEvents[$dateKey] = array();
            }
            
            $structuredEvents[$dateKey][] = $event;
        }
        
        ksort($structuredEvents);
        
        foreach ($structuredEvents as $dateKey => &$dateEvents)
        {
            usort($dateEvents, array($this, "orderEvents"));
        }
        
        return $structuredEvents;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\Event $eventLeft
     * @param \Chamilo\Libraries\Calendar\Event\Event $eventRight
     * @return integer
     */
    public function orderEvents($eventLeft, $eventRight)
    {
        return strcmp($eventLeft->getStartDate(), $eventRight->getStartDate());
    }

    /**
     *
     * @return integer
     */
    protected function getStartTime()
    {
        return $this->getDisplayTime();
    }

    /**
     *
     * @return integer
     */
    protected function getEndTime()
    {
        return strtotime('+6 Months', $this->getStartTime());
    }

    public function render()
    {
        $startTime = $this->getDisplayTime();
        $endTime = $events = $this->getEvents($this->getStartTime(), $this->getEndTime());
        
        $html = array();
        
        if (count($events) > 0)
        {
            $html[] = '<div class="table-calendar table-calendar-list">';
            
            foreach ($events as $dateKey => $dateEvents)
            {
                $hiddenEvents = 0;
                
                foreach ($dateEvents as $dateEvent)
                {
                    if (! $this->isSourceVisible($dateEvent->getSource()))
                    {
                        $hiddenEvents ++;
                    }
                }
                
                $allEventsAreHidden = ($hiddenEvents == count($dateEvents));
                
                $html[] = '<div class="row' . ($allEventsAreHidden ? ' event-container-hidden' : '') . '">';
                
                $html[] = '<div class="col-xs-12 table-calendar-list-date">';
                $html[] = date('D, d M', $dateKey);
                $html[] = '</div>';
                
                $html[] = '<div class="col-xs-12 table-calendar-list-events">';
                $html[] = '<ul class="list-group">';
                
                foreach ($dateEvents as $dateEvent)
                {
                    $eventRendererFactory = new HtmlTableRendererFactory($this, $dateEvent, $startTime);
                    
                    $html[] = '<li class="list-group-item ">';
                    $html[] = $eventRendererFactory->render();
                    $html[] = '</li>';
                }
                
                $html[] = '</ul>';
                $html[] = '</div>';
                
                $html[] = '</div>';
            }
            
            $html[] = '</div>';
        }
        else
        {
            $html[] = Display::normal_message(Translation::get('NoUpcomingEvents'), true);
        }
        
        return implode('', $html);
    }
}
