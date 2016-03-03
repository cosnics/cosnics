<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

use Chamilo\Libraries\Calendar\Renderer\Type\ViewRenderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class FullRenderer extends ViewRenderer
{

    /**
     *
     * @return string
     */
    public function renderMiniMonth()
    {
        $renderer = new MiniMonthRenderer(
            $this->getDataProvider(),
            $this->getLegend(),
            $this->getDisplayTime(),
            null,
            null);

        return $renderer->render();
    }

    /**
     *
     * @return string
     */
    abstract public function renderNavigation();

    /**
     *
     * @return string
     */
    abstract public function renderFullCalendar();

    /**
     *
     * @return string
     */
    abstract public function renderTitle();

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Renderer::render()
     */
    public function render()
    {
        $html = array();

        $html[] = '<div class="col-xs-12 col-lg-10 table-calendar-main">';

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12 col-lg-4">';
        $html[] = '<div class="pull-left">';
        $html[] = $this->renderNavigation();
        $html[] = '</div>';

        $html[] = '<div class="table-calendar-current-time pull-left">';
        $html[] = '<h4>';
        $html[] = $this->renderTitle();
        $html[] = '</h4>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-lg-8">';
        $html[] = '<div class="pull-right">';
        $html[] = $this->renderViewActions();
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->renderFullCalendar();
        $html[] = '</div>';

        $html[] = '<div class="col-md-3 col-lg-2 table-calendar-sidebar">';
        $html[] = $this->renderMiniMonth();
        $html[] = $this->getLegend()->render();
        $html[] = '</div>';

        $html[] = '<div class="clearfix"></div>';

        return implode(PHP_EOL, $html);
    }
}