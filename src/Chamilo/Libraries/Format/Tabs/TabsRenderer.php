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

    private ChamiloRequest $request;

    /**
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     */
    public function __construct(ChamiloRequest $request)
    {
        $this->request = $request;
    }

    public function render(string $name, TabsCollection $tabs): string
    {
        $html = [];

        if (!$tabs->isEmpty())
        {
            $html[] = $this->renderHeader($name, $tabs);

            // Tab content
            foreach ($tabs as $tab)
            {
                $html[] = $tab->render($name . '-' . $tab->getIdentifier());
            }

            $html[] = $this->renderFooter($name, $tabs);
        }

        return implode(PHP_EOL, $html);
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

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
     * @param \Chamilo\Libraries\Format\Tabs\TabsCollection<\Chamilo\Libraries\Format\Tabs\Form\FormTab> $tabs
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

        $selected_tab = $this->getSelectedTab($name, $tabs);

        if (isset($selected_tab))
        {
            $html[] = '$(\'#' . $name . 'Tabs a[href="#' . $selected_tab . '"]\').tab(\'show\');';
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
     * @param \Chamilo\Libraries\Format\Tabs\TabsCollection<\Chamilo\Libraries\Format\Tabs\Form\FormTab> $tabs
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
            $html[] = $tab->header();
        }

        $html[] = '</ul>';
        $html[] = '</div>';

        $html[] = '<div id="' . $name . 'TabsContent" class="tab-content dynamic-visual-tab-content">';

        return implode(PHP_EOL, $html);
    }

}
