<?php
namespace Chamilo\Libraries\UserInterface\Tab\Service;

use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidator;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\FormTab;
use HTML_QuickForm_html;

/**
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FormTabGenerator
{
    private GenericTabRenderer $genericTabRenderer;

    public function __construct(GenericTabRenderer $genericTabRenderer)
    {
        $this->genericTabRenderer = $genericTabRenderer;
    }

    public function getGenericTabRenderer(): GenericTabRenderer
    {
        return $this->genericTabRenderer;
    }

    /**
     * @throws \QuickformException
     */
    public function renderContent(string $formTabsGeneratorName, FormValidator $form, FormTab $tab): void
    {
        $form->addElement(
            HTML_QuickForm_html::class,
            $this->getGenericTabRenderer()->renderContentHeader($formTabsGeneratorName, $tab)
        );
        $this->renderContentSingleTab($form, $tab);
        $form->addElement(HTML_QuickForm_html::class, $this->getGenericTabRenderer()->renderContentFooter());
    }

    public function renderContentSingleTab(FormValidator $form, FormTab $tab): void
    {
        $method = $tab->getMethod();

        if (!is_array($method))
        {
            $method = [$form, $method];
        }

        call_user_func_array($method, $tab->getParameters());
    }

    public function renderNavigation(string $formTabsGeneratorName, FormTab $tab): string
    {
        return $this->getGenericTabRenderer()->renderNavigation($formTabsGeneratorName, $tab);
    }
}
