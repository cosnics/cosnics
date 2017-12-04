<?php
namespace Chamilo\Libraries\Calendar\Format\Renderer\Type;

use Chamilo\Libraries\Calendar\Format\HtmlTable\Calendar;
use Chamilo\Libraries\Calendar\Format\Renderer\FormatHtmlTableRenderer;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

/**
 *
 * @package Chamilo\Libraries\Calendar\Format\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniMonthRenderer extends FormatHtmlTableRenderer
{

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Format\Renderer\Renderer::render()
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
                    $eventRendererFactory = $this->getEventHtmlTableRendererFactory();
                    $calendar->addEvent(
                        $tableDate,
                        $eventRendererFactory->render(
                            $this->class_name(false),
                            $this->getDataProvider(),
                            $event,
                            $tableDate));
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

    /**
     *
     * @return string
     */
    public function determineNavigationUrl()
    {
        $parameters = $this->getDataProvider()->getDisplayParameters();
        $parameters[self::PARAM_TIME] = Calendar::TIME_PLACEHOLDER;

        $redirect = new Redirect($parameters);
        return $redirect->getUrl();
    }
}
