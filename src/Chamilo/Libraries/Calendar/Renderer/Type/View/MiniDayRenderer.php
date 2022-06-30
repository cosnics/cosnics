<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniDayRenderer extends DayRenderer
{

    public function render(): string
    {
        $html = [];
        $html[] = $this->renderFullCalendar();
        $html[] = $this->getLegend()->render();

        return implode(PHP_EOL, $html);
    }
}