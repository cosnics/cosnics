<?php
namespace Chamilo\Libraries\UserInterface\Tab\Service;

use Chamilo\Libraries\Architecture\Exception\ClassNotExistException;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabRendererRegistry;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Interface\TabContentInterface;

/**
 * @package Chamilo\Libraries\UserInterface\Tab\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TabsRenderer
{
    protected TabRendererRegistry $tabRendererRegistry;

    public function __construct(TabRendererRegistry $tabRendererRegistry)
    {
        $this->tabRendererRegistry = $tabRendererRegistry;
    }

    public function getTabRendererRegistry(): TabRendererRegistry
    {
        return $this->tabRendererRegistry;
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection<\Chamilo\Libraries\UserInterface\Tab\Architecture\Interface\TabContentInterface> $tabs
     */
    public function renderContent(TabsCollection $tabs, ?string $selectedTab = null): string
    {
        $html = [];

        $html[] = '<div class="tab-content">';

        foreach ($tabs as $tab) {
            try {
                if ($tab instanceof TabContentInterface) {
                    $tabRenderer = $this->tabRendererRegistry->getTabRendererForTab($tab);

                    $html[] = $tabRenderer->renderContent($tab, $selectedTab);
                }
            }
            catch (ClassNotExistException) {
            }
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection<\Chamilo\Libraries\UserInterface\Tab\Architecture\Interface\TabNavigationInterface> $tabs
     */
    public function renderNavigation(string $name, TabsCollection $tabs, ?string $selectedTab = null): string
    {
        $html = [];

        $html[] = '<ul class="nav nav-tabs mb-3"  id="' . $name . 'Tabs" role="tablist">';

        foreach ($tabs as $tab) {
            try {
                $tabRenderer = $this->tabRendererRegistry->getTabRendererForTab($tab);
                $html[] = $tabRenderer->renderNavigation($tab, $selectedTab);
            }
            catch (ClassNotExistException) {
            }
        }

        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection<\Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\GenericTab> $tabs
     */
    public function renderNavigationAndContent(string $name, TabsCollection $tabs, ?string $selectedTab = null): string
    {
        $html = [];

        if (!$tabs->isEmpty()) {
            $html[] = $this->renderNavigation($name, $tabs, $selectedTab);
            $html[] = $this->renderContent($tabs, $selectedTab);
        }

        return implode(PHP_EOL, $html);
    }
}
