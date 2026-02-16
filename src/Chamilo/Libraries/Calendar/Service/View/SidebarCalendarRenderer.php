<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Calendar\Service\JumpBarRenderer;
use Chamilo\Libraries\Calendar\Service\LegendRenderer;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Resource\ResourceManager;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service\View
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class SidebarCalendarRenderer extends HtmlCalendarRenderer
{
    protected JumpBarRenderer $jumpBarRenderer;

    protected MiniMonthCalendarRenderer $miniMonthCalendarRenderer;

    protected ResourceManager $resourceManager;

    protected WebPathBuilder $webPathBuilder;

    public function __construct(
        LegendRenderer $legendRenderer, UrlGenerator $urlGenerator, Translator $translator,
        MiniMonthCalendarRenderer $miniMonthCalendarRenderer, WebPathBuilder $webPathBuilder,
        ResourceManager $resourceManager, JumpBarRenderer $jumpBarRenderer, ButtonToolBarRenderer $buttonToolBarRenderer
    )
    {
        parent::__construct($legendRenderer, $urlGenerator, $translator, $buttonToolBarRenderer);

        $this->miniMonthCalendarRenderer = $miniMonthCalendarRenderer;
        $this->webPathBuilder = $webPathBuilder;
        $this->resourceManager = $resourceManager;
        $this->jumpBarRenderer = $jumpBarRenderer;
    }

    /**
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Event[] $events
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Visibility[] $invisibleSources
     *
     * @throws \Exception
     */
    public function render(
        array $events, array $displayParameters, int $displayTime, array $viewActions = [],
        array $invisibleSources = [], ?string $invisibilityContext = null
    ): string
    {
        $html = [];

        $html[] = '<div class="col-xs-12 col-lg-9 table-calendar-main">';

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12 col-lg-4">';
        $html[] = '<div class="pull-left">';
        $html[] = $this->renderNavigation($displayParameters, $displayTime);
        $html[] = '</div>';

        $html[] = '<div class="table-calendar-current-time pull-left">';
        $html[] = '<h4>';
        $html[] = $this->renderTitle($displayTime);
        $html[] = '</h4>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-lg-8">';
        $html[] = '<div class="pull-right">';
        $html[] = $this->renderViewActions($displayParameters, $viewActions);
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->renderFullCalendar(
            $events, $displayParameters, $displayTime, $invisibleSources, $invisibilityContext
        );
        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-lg-3 table-calendar-sidebar">';
        $html[] = $this->renderMiniMonth(
            $events, $displayParameters, $displayTime, $viewActions, $invisibleSources, $invisibilityContext
        );
        $html[] = $this->getLegendRenderer()->render($invisibleSources, $invisibilityContext);
        $html[] = $this->getJumpBarRenderer()->render(
            $this->determineNavigationUrl($displayParameters), $displayTime
        );
        $html[] = '</div>';

        $html[] = '<div class="clearfix"></div>';

        $html[] = $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath() . 'Calendar/EventTooltip.js'
        );

        return implode(PHP_EOL, $html);
    }

    protected function getJumpBarRenderer(): JumpBarRenderer
    {
        return $this->jumpBarRenderer;
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

    /**
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Event[] $events
     *
     * @throws \Exception
     */
    abstract public function renderFullCalendar(
        array $events, array $displayParameters, int $displayTime, array $invisibleSources = [],
        ?string $invisibilityContext = null
    ): string;

    /**
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Event[] $events
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Visibility[] $visibleSources
     *
     * @throws \Exception
     */
    public function renderMiniMonth(
        array $events, array $displayParameters, int $displayTime, array $viewActions = [],
        ?array $visibleSources = null, ?string $invisibilityContext = null
    ): string
    {
        return $this->getMiniMonthCalendarRenderer()->render(
            $events, $displayParameters, $displayTime, $viewActions, $visibleSources, $invisibilityContext
        );
    }

    /**
     * @param string[] $displayParameters
     */
    abstract public function renderNavigation(array $displayParameters, int $displayTime): string;

    abstract public function renderTitle(int $displayTime): string;
}