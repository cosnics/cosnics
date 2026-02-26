<?php
namespace Chamilo\Libraries\UserInterface\Tab\Service;

use Chamilo\Libraries\Architecture\Exception\ClassNotExistException;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabRendererRegistry;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection;

/**
 * @package Chamilo\Libraries\UserInterface\Tab\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TabsRenderer
{
    protected ActionsTabRenderer $actionsTabRenderer;

    protected ContentTabRenderer $contentTabRenderer;

    protected GenericTabRenderer $genericTabRenderer;

    protected TabRendererRegistry $tabRendererRegistry;

    public function __construct(
        TabRendererRegistry $tabRendererRegistry, ContentTabRenderer $contentTabRenderer,
        ActionsTabRenderer $actionsTabRenderer, GenericTabRenderer $tabRenderer
    )
    {
        $this->contentTabRenderer = $contentTabRenderer;
        $this->actionsTabRenderer = $actionsTabRenderer;
        $this->genericTabRenderer = $tabRenderer;
        $this->tabRendererRegistry = $tabRendererRegistry;
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection<\Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\GenericTab> $tabs
     */
    public function render(string $name, TabsCollection $tabs, ?string $selectedTab = null): string
    {
        $html = [];

        if (!$tabs->isEmpty()) {
            $html[] = $this->renderHeader($name, $tabs, $selectedTab);
            $html[] = $this->renderContent($tabs, $selectedTab);
            $html[] = $this->renderFooter();
        }

        return implode(PHP_EOL, $html);
    }

    public function getActionsTabRenderer(): ActionsTabRenderer
    {
        return $this->actionsTabRenderer;
    }

    public function getContentTabRenderer(): ContentTabRenderer
    {
        return $this->contentTabRenderer;
    }

    public function getGenericTabRenderer(): GenericTabRenderer
    {
        return $this->genericTabRenderer;
    }

    public function getTabRendererRegistry(): TabRendererRegistry
    {
        return $this->tabRendererRegistry;
    }

    protected function renderContent(TabsCollection $tabs, ?string $selectedTab = null): string
    {
        $html = [];

        foreach ($tabs as $tab) {
            try {
                $tabRenderer = $this->tabRendererRegistry->getTabRendererForTab($tab);
                $html[] = $tabRenderer->renderContent($tab, $selectedTab);
            }
            catch (ClassNotExistException) {
            }
        }

        return implode(PHP_EOL, $html);
    }

    public function renderFooter(): string
    {
        return '</div>';
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection<\Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\GenericTab> $tabs
     */
    public function renderHeader(string $name, TabsCollection $tabs, ?string $selectedTab = null): string
    {
        $html = [];

        $html[] = '<ul class="nav nav-tabs"  id="' . $name . 'Tabs" role="tablist">';

        foreach ($tabs as $tab) {
            try {
                $tabRenderer = $this->tabRendererRegistry->getTabRendererForTab($tab);
                $html[] = $tabRenderer->renderNavigation($tab, $selectedTab);
            }
            catch (ClassNotExistException) {
            }
        }

        $html[] = '</ul>';
        $html[] = '<div class="tab-content">';

        return implode(PHP_EOL, $html);
    }
}
