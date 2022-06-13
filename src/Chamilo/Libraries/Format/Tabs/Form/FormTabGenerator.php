<?php
namespace Chamilo\Libraries\Format\Tabs\Form;

use Chamilo\Libraries\Format\Form\FormValidator;

/**
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FormTabGenerator
{

    public function renderContent(string $formTabsGeneratorName, FormValidator $form, FormTab $tab)
    {
        $form->addElement('html', $this->renderContentHeader($formTabsGeneratorName, $tab));
        $this->renderContentSingleTab($form, $tab);
        $form->addElement('html', $this->renderContentFooter());
    }

    protected function renderContentFooter(): string
    {
        $html = [];

        $html[] = '<div class="clearfix"></div>';

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    protected function renderContentHeader(string $formTabsGeneratorName, FormTab $tab): string
    {
        $html = [];

        $html[] =
            '<div role="tabpanel" class="tab-pane" id="' . $formTabsGeneratorName . '-' . $tab->getIdentifier() . '">';
        $html[] = '<div class="list-group-item">';

        return implode(PHP_EOL, $html);
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
        $html = [];
        $html[] = '<li>';
        $html[] =
            '<a title="' . htmlentities(strip_tags($tab->getLabel())) . '" href="#' . $formTabsGeneratorName . '-' .
            $tab->getIdentifier() . '">';
        $html[] = '<span class="category">';

        if ($tab->getInlineGlyph() && $tab->isIconVisible())
        {
            $html[] = $tab->getInlineGlyph()->render();
        }

        if ($tab->getLabel() && $tab->isTextVisible())
        {
            $html[] = '<span class="title">' . $tab->getLabel() . '</span>';
        }

        $html[] = '</span>';
        $html[] = '</a>';
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }
}
