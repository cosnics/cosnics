<?php
namespace Chamilo\Libraries\Format\Tabs\Form;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Tabs\GenericTabsRenderer;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use HTML_QuickForm_html;

/**
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FormTabsGenerator
{
    private FormTabGenerator $formTabGenerator;

    private GenericTabsRenderer $genericTabsRenderer;

    public function __construct(GenericTabsRenderer $genericTabsRenderer, FormTabGenerator $formTabGenerator)
    {
        $this->genericTabsRenderer = $genericTabsRenderer;
        $this->formTabGenerator = $formTabGenerator;
    }

    /**
     * @param \Chamilo\Libraries\Format\Tabs\TabsCollection<\Chamilo\Libraries\Format\Tabs\Form\FormTab> $tabs
     *
     * @throws \QuickformException
     */
    public function generate(string $name, FormValidator $form, TabsCollection $tabs): void
    {
        if ($tabs->hasMultipleTabs())
        {
            $form->addElement(HTML_QuickForm_html::class, $this->renderHeader($name, $tabs));
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
            $form->addElement(HTML_QuickForm_html::class, $this->getGenericTabsRenderer()->renderFooter($name, $tabs));
        }
    }

    public function getFormTabGenerator(): FormTabGenerator
    {
        return $this->formTabGenerator;
    }

    public function getGenericTabsRenderer(): GenericTabsRenderer
    {
        return $this->genericTabsRenderer;
    }

    /**
     * @param \Chamilo\Libraries\Format\Tabs\TabsCollection<\Chamilo\Libraries\Format\Tabs\Form\FormTab> $tabs
     */
    protected function renderHeader(string $name, TabsCollection $tabs): string
    {
        $html = [];

        $html[] = $this->getGenericTabsRenderer()->renderHeaderTop($name);

        foreach ($tabs as $tab)
        {
            $html[] = $this->getFormTabGenerator()->renderNavigation($name, $tab);
        }

        $html[] = $this->getGenericTabsRenderer()->renderHeaderBottom($name);

        return implode(PHP_EOL, $html);
    }
}
