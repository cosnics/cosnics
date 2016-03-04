<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

use Chamilo\Libraries\Calendar\Renderer\Event\EventRendererFactory;
use Chamilo\Libraries\Calendar\Renderer\Type\ViewRenderer;
use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\ActionBar\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ListRenderer extends ViewRenderer
{

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\ViewRenderer::getEvents()
     */
    public function getEvents($startTime, $endTime)
    {
        $events = parent :: getEvents($startTime, $endTime);

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
     * @see \Chamilo\Libraries\Calendar\Renderer\Renderer::render()
     */
    public function render()
    {
        $html = array();

        $html = array();

        $html[] = '<div class="col-md-12 col-lg-10 table-list-main">';

        $html[] = '<div class="row">';
        $html[] = '<div class="col-md-4">';
        $html[] = '<div class="pull-left">';
        $html[] = $this->renderNavigation();
        $html[] = '</div>';

        $html[] = '<div class="table-calendar-current-time pull-left">';
        $html[] = '<h4>';
        $html[] = $this->renderTitle();
        $html[] = '</h4>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="col-md-8">';
        $html[] = '<div class="pull-right">';
        $html[] = $this->renderViewActions();
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->renderEvents();
        $html[] = '</div>';

        $html[] = '<div class="col-md-12 col-lg-2 table-list-sidebar">';
        // $html[] = $this->renderMiniMonth();
        $html[] = $this->getLegend()->render();
        $html[] = '</div>';

        $html[] = '<div class="clearfix"></div>';

        return implode(PHP_EOL, $html);
    }

    private function renderEvents()
    {
        $startTime = $this->getDisplayTime();
        $endTime = strtotime('+6 Months', $startTime);
        $events = $this->getEvents($startTime, $endTime);

        $html = array();

        if (count($events) > 0)
        {
            $html[] = '<table class="table-calendar table-calendar-list">';
            $html[] = '<tbody>';

            foreach ($events as $dateKey => $dateEvents)
            {
                $html[] = '<tr>';

                $html[] = '<td class="table-calendar-list-date">';
                $html[] = date('D, d M', $dateKey);
                $html[] = '</td>';

                $html[] = '<td class="table-calendar-list-events">';
                $html[] = '<ul class="list-group">';

                foreach ($dateEvents as $dateEvent)
                {
                    $eventRendererFactory = new EventRendererFactory($this, $dateEvent);

                    $html[] = '<li class="list-group-item ">';
                    $html[] = $eventRendererFactory->render();
                    $html[] = '</li>';
                }

                $html[] = '</ul>';
                $html[] = '</td>';

                $html[] = '</tr>';
            }

            $html[] = '</tbody>';
            $html[] = '</table>';
        }
        else
        {
            $html[] = Display :: normal_message(Translation :: get('NoUpcomingEvents'), true);
        }

        return implode('', $html);
    }

    /**
     * Adds a navigation bar to the calendar
     *
     * @param string $urlFormat The *TIME* in this string will be replaced by a timestamp
     */
    public function renderNavigation()
    {
        $urlFormat = $this->determineNavigationUrl();
        $todayUrl = str_replace(Calendar :: TIME_PLACEHOLDER, time(), $urlFormat);

        $buttonToolBar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();

        $buttonToolBar->addItem(
            new Button(Translation :: get('Today'), new BootstrapGlyph('home'), $todayUrl, Button :: DISPLAY_ICON));

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);
        return $buttonToolbarRenderer->render();
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\View\FullTableRenderer::renderTitle()
     */
    public function renderTitle()
    {
        $startTime = $this->getDisplayTime();
        $endTime = strtotime('+6 Months', $startTime);

        return date('d M Y', $startTime) . ' - ' . date('d M Y', $endTime);
    }
}
