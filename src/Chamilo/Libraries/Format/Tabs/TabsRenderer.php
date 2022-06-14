<?php
namespace Chamilo\Libraries\Format\Tabs;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TabsRenderer
{
    public const PARAM_SELECTED_TAB = 'tab';

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
     * @param string $name
     * @param \Chamilo\Libraries\Format\Tabs\TabsCollection<\Chamilo\Libraries\Format\Tabs\GenericTab> $tabs
     *
     * @return string
     */
    public function render(string $name, TabsCollection $tabs): string
    {
        $html = [];

        if (!$tabs->isEmpty())
        {
            $html[] = $this->renderHeader($name, $tabs);

            foreach ($tabs as $tab)
            {
                switch (get_class($tab))
                {
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
     * @param string $name
     * @param \Chamilo\Libraries\Format\Tabs\TabsCollection<\Chamilo\Libraries\Format\Tabs\GenericTab> $tabs
     *
     * @return string
     */
    public function renderHeader(string $name, TabsCollection $tabs): string
    {
        $html = [];

        $html[] = $this->getGenericTabsRenderer()->renderHeaderTop($name);

        foreach ($tabs as $tab)
        {
            switch (get_class($tab))
            {
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
