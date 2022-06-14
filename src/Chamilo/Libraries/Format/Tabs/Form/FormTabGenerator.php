<?php
namespace Chamilo\Libraries\Format\Tabs\Form;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Tabs\GenericTabRenderer;

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

    public function renderContent(string $formTabsGeneratorName, FormValidator $form, FormTab $tab)
    {
        $form->addElement('html', $this->getGenericTabRenderer()->renderContentHeader($formTabsGeneratorName, $tab));
        $this->renderContentSingleTab($form, $tab);
        $form->addElement('html', $this->getGenericTabRenderer()->renderContentFooter());
    }

    public function renderContentSingleTab(FormValidator $form, FormTab $tab)
    {
        $method = $tab->getMethod();

        if (!is_array($method))
        {
            $method = array($form, $method);
        }

        call_user_func_array($method, $tab->getParameters());
    }

    public function renderNavigation(string $formTabsGeneratorName, FormTab $tab): string
    {
        return $this->getGenericTabRenderer()->renderNavigation($formTabsGeneratorName, $tab);
    }
}
