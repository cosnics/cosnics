<?php
namespace Chamilo\Libraries\UserInterface\Tab\Service;

use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\ActionsTab;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\ContentTab;
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

    public function __construct(
        ContentTabRenderer $contentTabRenderer, ActionsTabRenderer $actionsTabRenderer, GenericTabRenderer $tabRenderer
    )
    {
        $this->contentTabRenderer = $contentTabRenderer;
        $this->actionsTabRenderer = $actionsTabRenderer;
        $this->genericTabRenderer = $tabRenderer;
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

    protected function renderContent( TabsCollection $tabs): string
    {
        $html = [];

        foreach ($tabs as $tab) {
            switch (get_class($tab)) {
                case ContentTab::class:
                    $html[] = $this->getContentTabRenderer()->renderContent($tab);
                    break;
                case ActionsTab::class:
                    $html[] = $this->getActionsTabRenderer()->renderContent($tab);
                    break;
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
            $html[] = $this->getGenericTabRenderer()->renderNavigation($tab, $selectedTab);
        }

        $html[] = '</ul>';
        $html[] = '<div class="tab-content">';

        return implode(PHP_EOL, $html);
    }
}
