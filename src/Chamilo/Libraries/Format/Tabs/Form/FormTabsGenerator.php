<?php
namespace Chamilo\Libraries\Format\Tabs\Form;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Platform\ChamiloRequest;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FormTabsGenerator
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

    /**
     * @param string $name
     * @param \Chamilo\Libraries\Format\Form\FormValidator $form
     * @param \Chamilo\Libraries\Format\Tabs\TabsCollection<\Chamilo\Libraries\Format\Tabs\Form\FormTab> $tabs
     */
    public function generate(string $name, FormValidator $form, TabsCollection $tabs)
    {
        if ($tabs->hasMultipleTabs())
        {
            $form->addElement('html', $this->renderHeader($name, $tabs));
        }

        foreach ($tabs as $tab)
        {
            $tab->setForm($form);
            $tab->render($tabs->hasOnlyOneTab());
        }

        if ($tabs->hasMultipleTabs())
        {
            $form->addElement('html', $this->renderFooter($name, $tabs));
        }
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

    protected function renderFooter(string $name, TabsCollection $tabs): string
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
            $html[] = '$(\'#' . $name . 'Tabs a[href="#' . $name . '-' . $selectedTab . '"]\').tab(\'show\');';
        }
        else
        {
            $html[] = '$(\'#' . $name . 'Tabs a:first\').tab(\'show\')';
        }

        $html[] = '</script>';

        return implode(PHP_EOL, $html);
    }

    protected function renderHeader(string $name, TabsCollection $tabs): string
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
