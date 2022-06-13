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

    private FormTabGenerator $formTabGenerator;

    private ChamiloRequest $request;

    public function __construct(ChamiloRequest $request, FormTabGenerator $formTabGenerator)
    {
        $this->request = $request;
        $this->formTabGenerator = $formTabGenerator;
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
            if ($tabs->hasOnlyOneTab())
            {
                $this->getFormTabGenerator()->renderContentSingleTab($form, $tab);
            }
            else
            {
                $this->getFormTabGenerator()->renderContent($name, $form, $tab);
            }
        }

        if ($tabs->hasMultipleTabs())
        {
            $form->addElement('html', $this->renderFooter($name, $tabs));
        }
    }

    public function getFormTabGenerator(): FormTabGenerator
    {
        return $this->formTabGenerator;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    /**
     * @param string $name
     * @param \Chamilo\Libraries\Format\Tabs\TabsCollection<\Chamilo\Libraries\Format\Tabs\Form\FormTab> $tabs
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
     * @param \Chamilo\Libraries\Format\Tabs\TabsCollection<\Chamilo\Libraries\Format\Tabs\Form\FormTab> $tabs
     *
     * @return string
     */
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

    /**
     * @param string $name
     * @param \Chamilo\Libraries\Format\Tabs\TabsCollection<\Chamilo\Libraries\Format\Tabs\Form\FormTab> $tabs
     *
     * @return string
     */
    protected function renderHeader(string $name, TabsCollection $tabs): string
    {
        $html = [];

        $html[] = '<div id="' . $name . 'Tabs">';

        // Tab headers
        $html[] = '<ul class="nav nav-tabs tabs-header dynamic-visual-tabs">';

        foreach ($tabs as $tab)
        {
            $html[] = $this->getFormTabGenerator()->renderNavigation($name, $tab);
        }

        $html[] = '</ul>';
        $html[] = '</div>';

        $html[] = '<div id="' . $name . 'TabsContent" class="tab-content dynamic-visual-tab-content">';

        return implode(PHP_EOL, $html);
    }
}
