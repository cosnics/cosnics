<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View\Table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class MiniDayRenderer extends DayRenderer
{

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Renderer::render()
     */
    public function render()
    {
        $html = array();
        $html[] = $this->renderFullCalendar();
        $html[] = $this->getLegend()->render();

        return implode(PHP_EOL, $html);
    }
}