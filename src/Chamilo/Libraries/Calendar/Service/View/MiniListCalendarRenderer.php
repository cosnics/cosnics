<?php
namespace Chamilo\Libraries\Calendar\Service\View;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniListCalendarRenderer extends ListCalendarRenderer
{

    public function render(): string
    {
        $html = [];

        $html[] = '<h4>';
        $html[] = $this->renderTitle();
        $html[] = '</h4>';

        $html[] = $this->renderFullCalendar();
        $html[] = $this->getLegend()->render();

        $html[] = '<div class="clearfix"></div>';

        return implode(PHP_EOL, $html);
    }

    protected function getEndTime(): int
    {
        return strtotime('+3 Days', $this->getStartTime());
    }
}
