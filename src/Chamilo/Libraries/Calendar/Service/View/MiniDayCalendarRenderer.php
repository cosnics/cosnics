<?php
namespace Chamilo\Libraries\Calendar\Service\View;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniDayCalendarRenderer extends DayCalendarRenderer
{

    public function render(): string
    {
        $html = [];
        $html[] = $this->renderFullCalendar();
        $html[] = $this->getLegend()->render();

        return implode(PHP_EOL, $html);
    }
}