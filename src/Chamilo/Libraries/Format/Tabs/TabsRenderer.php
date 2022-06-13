<?php
namespace Chamilo\Libraries\Format\Tabs;

use Chamilo\Libraries\Platform\ChamiloRequest;

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

    private ChamiloRequest $request;

    public function __construct(
        ChamiloRequest $request, ContentTabRenderer $contentTabRenderer, ActionsTabRenderer $actionsTabRenderer
    )
    {
        $this->request = $request;
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

            // Tab content
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

            $html[] = $this->renderFooter($name, $tabs);
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

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    /**
     * @param string $name
     * @param \Chamilo\Libraries\Format\Tabs\TabsCollection<\Chamilo\Libraries\Format\Tabs\GenericTab> $tabs
     *
     * @return ?string
     */
    protected function getSelectedTab(string $name, TabsCollection $tabs): ?string
    {
        $selectedTabs = $this->getRequest()->query->get(self::PARAM_SELECTED_TAB);

        if (is_array($selectedTabs))
        {
            $selectedTab = $selectedTabs[$name];

            if (!is_null($selectedTab) && $tabs->isValidIdentifier($selectedTab))
            {
                return $selectedTab;
            }
        }

        return null;
    }

    /**
     * @param string $name
     * @param \Chamilo\Libraries\Format\Tabs\TabsCollection<\Chamilo\Libraries\Format\Tabs\GenericTab> $tabs
     *
     * @return string
     */
    public function renderFooter(string $name, TabsCollection $tabs): string
    {
        $html = [];

        $html[] = '</div>';
        $html[] = '<script>';

        $html[] = '$(\'#' . $name . 'Tabs a\').click(function (e) {
  e.preventDefault()
  $(this).tab(\'show\')
})';

        $selectedTab = $this->getSelectedTab($name, $tabs);

        if (isset($selectedTab))
        {
            $html[] = '$(\'#' . $name . 'Tabs a[href="#' . $selectedTab . '"]\').tab(\'show\');';
        }
        else
        {
            $html[] = '$(\'#' . $name . 'Tabs a:first\').tab(\'show\')';
        }

        $html[] = '</script>';

        return implode(PHP_EOL, $html);
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

        $html[] = '<div id="' . $name . 'Tabs">';

        // Tab headers
        $html[] = '<ul class="nav nav-tabs tabs-header dynamic-visual-tabs">';

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

        $html[] = '</ul>';
        $html[] = '</div>';

        $html[] = '<div id="' . $name . 'TabsContent" class="tab-content dynamic-visual-tab-content">';

        return implode(PHP_EOL, $html);
    }

}
