<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Form\JumpForm;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class SidebarCalendarRenderer extends HtmlCalendarRenderer
{

    /**
     * @throws \Exception
     */
    public function render(
        CalendarRendererProviderInterface $dataProvider, int $displayTime, array $viewActions = [],
        string $linkTarget = ''
    ): string
    {
        $html = [];

        $html[] = '<div class="col-xs-12 col-lg-9 table-calendar-main">';

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12 col-lg-4">';
        $html[] = '<div class="pull-left">';
        $html[] = $this->renderNavigation($displayTime);
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

        $html[] = '<div class="col-xs-12 col-lg-3 table-calendar-sidebar">';
        //        $html[] = $this->renderMiniMonth();
        //        $html[] = $this->getLegend()->render();
        //        $html[] = $this->getJumpForm()->render();
        $html[] = '</div>';

        $html[] = '<div class="clearfix"></div>';

        $html[] = ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries\Calendar\Renderer', true) . 'EventTooltip.js'
        );

        return implode(PHP_EOL, $html);
    }

    protected function getJumpForm(): JumpForm
    {
        if (!isset($this->form))
        {
            $this->form = new JumpForm($this->determineNavigationUrl(), $this->getDisplayTime());
        }

        return $this->form;
    }

    abstract public function renderFullCalendar(): string;

    /**
     * @throws \Exception
     */
    public function renderMiniMonth(): string
    {
        $renderer = new MiniMonthRenderer(
            $this->getDataProvider(), $this->getLegend(), $this->getDisplayTime()
        );

        return $renderer->render();
    }

    abstract public function renderNavigation(int $displayTime): string;

    abstract public function renderTitle(): string;
}