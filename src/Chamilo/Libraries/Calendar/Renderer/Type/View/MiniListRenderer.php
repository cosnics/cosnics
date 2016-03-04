<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class MiniListRenderer extends ListRenderer
{

    /**
     *
     * @return integer
     */
    protected function getEndTime()
    {
        return strtotime('+3 Days', $this->getStartTime());
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Renderer::render()
     */
    public function render()
    {
        $html = array();

        $html[] = '<h4>';
        $html[] = $this->renderTitle();
        $html[] = '</h4>';

        $html[] = $this->renderFullCalendar();

        $html[] = $this->getLegend()->render();

        $html[] = '<div class="clearfix"></div>';

        return implode(PHP_EOL, $html);
    }
}
