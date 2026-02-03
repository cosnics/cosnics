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
    private ActionsTabRenderer $actionsTabRenderer;

    private ContentTabRenderer $contentTabRenderer;

    private GenericTabsRenderer $genericTabsRenderer;

    public function __construct(
        GenericTabsRenderer $genericTabsRenderer, ContentTabRenderer $contentTabRenderer,
        ActionsTabRenderer $actionsTabRenderer
    )
    {
        $this->genericTabsRenderer = $genericTabsRenderer;
        $this->contentTabRenderer = $contentTabRenderer;
        $this->actionsTabRenderer = $actionsTabRenderer;
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection<\Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\GenericTab> $tabs
     */
    public function render(string $name, TabsCollection $tabs): string
    {
        $html = [];

        if (!$tabs->isEmpty()) {
            $html[] = $this->renderHeader($name, $tabs);

            foreach ($tabs as $tab) {
                switch (get_class($tab)) {
                    case ContentTab::class:
                        $html[] = $this->getContentTabRenderer()->renderContent($name, $tab);
                        break;
                    case ActionsTab::class:
                        $html[] = $this->getActionsTabRenderer()->renderContent($name, $tab);
                        break;
                }
            }

            $html[] = $this->getGenericTabsRenderer()->renderFooter($name, $tabs);
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

    public function getGenericTabsRenderer(): GenericTabsRenderer
    {
        return $this->genericTabsRenderer;
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection<\Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\GenericTab> $tabs
     */
    public function renderHeader(string $name, TabsCollection $tabs): string
    {
        $html = [];

        $html[] = $this->getGenericTabsRenderer()->renderHeaderTop($name);

        foreach ($tabs as $tab) {
            switch (get_class($tab)) {
                case ContentTab::class:
                    $html[] = $this->getContentTabRenderer()->renderNavigation($name, $tab);
                    break;
                case ActionsTab::class:
                    $html[] = $this->getActionsTabRenderer()->renderNavigation($name, $tab);
                    break;
            }
        }

        $html[] = $this->getGenericTabsRenderer()->renderHeaderBottom($name);

        return implode(PHP_EOL, $html);
    }
}
