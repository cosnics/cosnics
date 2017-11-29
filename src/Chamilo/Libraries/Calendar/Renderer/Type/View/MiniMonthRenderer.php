<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

use Chamilo\Libraries\Calendar\Renderer\Event\EventRendererFactory;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniMonthRenderer extends HtmlTableRenderer
{

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Table\Type\MiniMonthCalendar
     */
    public function initializeCalendar()
    {
        return $this->getMiniMonthCalendarBuilder()->buildCalendar(
            $this->getDisplayTime(),
            [],
            array('table-calendar-mini'));
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Service\Table\MiniMonthCalendarBuilder
     */
    protected function getMiniMonthCalendarBuilder()
    {
        return $this->getService('chamilo.libraries.calendar.service.html_table.mini_month_calendar_builder');
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Table\CalendarConfiguration
     */
    protected function getCalendarConfiguration()
    {
        return $this->getService('chamilo.libraries.calendar.calendar_configuration');
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        $calendar = $this->getCalendar();

        $startTime = $calendar->getStartTime();
        $endTime = $calendar->getEndTime();

        $events = $this->getEvents($startTime, $endTime);
        $tableDate = $startTime;

        while ($tableDate <= $endTime)
        {
            $nextTableDate = strtotime('+1 Day', $tableDate);

            foreach ($events as $index => $event)
            {
                $startDate = $event->getStartDate();
                $endDate = $event->getEndDate();

                if ($tableDate < $startDate && $startDate < $nextTableDate ||
                     $tableDate < $endDate && $endDate <= $nextTableDate ||
                     $startDate <= $tableDate && $nextTableDate <= $endDate)
                {
                    $this->getLegend()->addSource($event->getSource());

                    $configuration = new \Chamilo\Libraries\Calendar\Renderer\Event\Configuration();
                    $configuration->setStartDate($tableDate);

                    $eventRendererFactory = new EventRendererFactory($this, $event, $configuration);
                    $calendar->addEvent($tableDate, $eventRendererFactory->render());
                }
            }

            $tableDate = $nextTableDate;
        }

        $calendar->addNavigationLinks($this->determineNavigationUrl());

        $html = array();

        $html[] = '<div class="table-calendar-mini-container">';
        $html[] = $calendar->render();
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';

        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries\Calendar\Renderer', true) . 'EventTooltip.js');

        return implode(PHP_EOL, $html);
    }
}
