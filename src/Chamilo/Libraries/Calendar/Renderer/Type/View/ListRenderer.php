<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

use Chamilo\Libraries\Calendar\Renderer\Event\EventRendererFactory;
use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ListRenderer extends FullRenderer
{

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\ViewRenderer::getEvents()
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

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\View\FullRenderer::renderFullCalendar()
     */
    public function renderFullCalendar()
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

                $html[] = '<div class="col table-calendar-list-date">';
                $html[] = date('D, d M', $dateKey);
                $html[] = '</div>';

                $html[] = '<div class="col table-calendar-list-events">';
                $html[] = '<ul class="list-group">';

                foreach ($dateEvents as $dateEvent)
                {
                    $eventRendererFactory = new EventRendererFactory($this, $dateEvent);

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

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\View\FullRenderer::renderNavigation()
     */
    public function renderNavigation()
    {
        $urlFormat = $this->determineNavigationUrl();
        $todayUrl = str_replace(Calendar::TIME_PLACEHOLDER, time(), $urlFormat);

        $buttonToolBar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();

        $buttonToolBar->addItem(
            new Button(Translation::get('Today'), new FontAwesomeGlyph('home'), $todayUrl, Button::DISPLAY_ICON));

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);
        return $buttonToolbarRenderer->render();
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\View\FullRenderer::renderTitle()
     */
    public function renderTitle()
    {
        return date('d M Y', $this->getStartTime()) . ' - ' . date('d M Y', $this->getEndTime());
    }
}
