<?php
namespace Chamilo\Libraries\Format\Tabs;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActionsTabRenderer
{
    private ActionRenderer $actionRenderer;

    private GenericTabRenderer $tabRenderer;

    public function __construct(GenericTabRenderer $tabRenderer, ActionRenderer $actionRenderer)
    {
        $this->tabRenderer = $tabRenderer;
        $this->actionRenderer = $actionRenderer;
    }

    public function getActionRenderer(): ActionRenderer
    {
        return $this->actionRenderer;
    }

    public function getTabRenderer(): GenericTabRenderer
    {
        return $this->tabRenderer;
    }

    public function renderContent(string $tabsRendererName, ActionsTab $tab): string
    {
        $html = [];

        $html[] = $this->getTabRenderer()->renderContentHeader($tabsRendererName, $tab);

        foreach ($tab->getActions() as $action)
        {
            $html[] = $this->getActionRenderer()->render($action);
        }

        $html[] = $this->getTabRenderer()->renderContentFooter();

        return implode(PHP_EOL, $html);
    }

    public function renderNavigation(string $tabsRendererName, ActionsTab $tab): string
    {
        return $this->getTabRenderer()->renderNavigation($tabsRendererName, $tab);
    }
}