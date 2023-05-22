<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Form\JumpForm;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service\View
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class SidebarCalendarRenderer extends HtmlCalendarRenderer
{
    protected MiniMonthCalendarRenderer $miniMonthCalendarRenderer;

    protected ResourceManager $resourceManager;

    protected WebPathBuilder $webPathBuilder;

    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        MiniMonthCalendarRenderer $miniMonthCalendarRenderer, WebPathBuilder $webPathBuilder,
        ResourceManager $resourceManager
    )
    {
        parent::__construct($legendRenderer, $urlGenerator, $translator);

        $this->miniMonthCalendarRenderer = $miniMonthCalendarRenderer;
        $this->webPathBuilder = $webPathBuilder;
        $this->resourceManager = $resourceManager;
    }

    /**
     * @throws \Exception
     */
    public function render(CalendarRendererProviderInterface $dataProvider, int $displayTime, array $viewActions = []
    ): string
    {
        $html = [];

        $html[] = '<div class="col-xs-12 col-lg-9 table-calendar-main">';

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12 col-lg-4">';
        $html[] = '<div class="pull-left">';
        $html[] = $this->renderNavigation($dataProvider, $displayTime);
        $html[] = '</div>';

        $html[] = '<div class="table-calendar-current-time pull-left">';
        $html[] = '<h4>';
        $html[] = $this->renderTitle($dataProvider, $displayTime);
        $html[] = '</h4>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-lg-8">';
        $html[] = '<div class="pull-right">';
        $html[] = $this->renderViewActions($dataProvider, $viewActions);
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->renderFullCalendar($dataProvider, $displayTime);
        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-lg-3 table-calendar-sidebar">';
        $html[] = $this->renderMiniMonth($dataProvider, $displayTime, $viewActions);
        $html[] = $this->getLegendRenderer()->render($dataProvider);
        $html[] = $this->getJumpForm($dataProvider, $displayTime)->render();
        $html[] = '</div>';

        $html[] = '<div class="clearfix"></div>';

        $html[] = $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath('Chamilo\Libraries\Calendar') . 'EventTooltip.js'
        );

        return implode(PHP_EOL, $html);
    }

    protected function getJumpForm(CalendarRendererProviderInterface $dataProvider, int $displayTime): JumpForm
    {
        if (!isset($this->form))
        {
            $this->form = new JumpForm($this->determineNavigationUrl($dataProvider), $displayTime);
        }

        return $this->form;
    }

    public function getMiniMonthCalendarRenderer(): MiniMonthCalendarRenderer
    {
        return $this->miniMonthCalendarRenderer;
    }

    public function getResourceManager(): ResourceManager
    {
        return $this->resourceManager;
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }

    abstract public function renderFullCalendar(CalendarRendererProviderInterface $dataProvider, int $displayTime
    ): string;

    /**
     * @throws \Exception
     */
    public function renderMiniMonth(
        CalendarRendererProviderInterface $dataProvider, int $displayTime, array $viewActions = []
    ): string
    {
        return $this->getMiniMonthCalendarRenderer()->render($dataProvider, $displayTime, $viewActions);
    }

    abstract public function renderNavigation(CalendarRendererProviderInterface $dataProvider, int $displayTime
    ): string;

    abstract public function renderTitle(CalendarRendererProviderInterface $dataProvider, int $displayTime): string;
}