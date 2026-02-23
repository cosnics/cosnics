<?php
namespace Chamilo\Libraries\UserInterface\Tab\Service;

use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidator;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection;
use HTML_QuickForm_html;

/**
 * @package Chamilo\Libraries\UserInterface\Tab\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FormTabsGenerator
{
    private FormTabGenerator $formTabGenerator;

    public function __construct(FormTabGenerator $formTabGenerator)
    {
        $this->formTabGenerator = $formTabGenerator;
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection<\Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\FormTab> $tabs
     *
     * @throws \QuickformException
     */
    public function generate(string $name, FormValidator $form, TabsCollection $tabs): void
    {
        if ($tabs->hasMultipleTabs()) {
            $form->addElement(HTML_QuickForm_html::class, $this->renderHeader($name, $tabs));
        }

        foreach ($tabs as $tab) {
            if ($tabs->hasOnlyOneTab()) {
                $this->getFormTabGenerator()->renderContentSingleTab($form, $tab);
            }
            else {
                $this->getFormTabGenerator()->renderContent($form, $tab);
            }
        }

        if ($tabs->hasMultipleTabs()) {
            $form->addElement(HTML_QuickForm_html::class, '</div>');
        }
    }

    public function getFormTabGenerator(): FormTabGenerator
    {
        return $this->formTabGenerator;
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection<\Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\FormTab> $tabs
     */
    protected function renderHeader(string $name, TabsCollection $tabs): string
    {
        $html = [];

        $html[] = '<ul class="nav nav-tabs"  id="' . $name . 'Tabs" role="tablist">';

        foreach ($tabs as $tab) {
            $html[] = $this->getFormTabGenerator()->renderNavigation($tab);
        }

        $html[] = '</ul>';
        $html[] = '<div class="tab-content">';

        return implode(PHP_EOL, $html);
    }
}
