<?php
namespace Chamilo\Libraries\Format\Form\FormType;

use Chamilo\Libraries\Format\Form\FormValidatorHtmlEditor;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Translation\Translation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type to declare the html_editor form type
 *
 * @package Chamilo\Libraries\Format\Form\FormType
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class HtmlEditorFormType extends AbstractType
{

    /**
     *
     * @see \Symfony\Component\Form\AbstractType::setDefaultOptions()
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('html_editor_options' => array(), 'html_editor_attributes' => array()));

        $resolver->setAllowedTypes(
            array('html_editor_options' => array('array'), 'html_editor_attributes' => array('array')));
    }

    /**
     *
     * @see \Symfony\Component\Form\AbstractType::buildView()
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $label = ! empty($view->vars['label']) ? $view->vars['label'] : Translation::get($view->vars['name']);

        $html_editor = FormValidatorHtmlEditor::factory(
            LocalSetting::get('html_editor'),
            $view->vars['full_name'],
            $label,
            false,
            $options['html_editor_options'],
            $options['html_editor_attributes']);

        $javascript = array();

        $includes = $html_editor->get_includes();
        foreach ($includes as $include)
        {
            if (! empty($include))
            {
                $javascript[] = $include;
            }
        }

        $javascript = array_merge($javascript, $html_editor->get_javascript());

        $view->vars['html_editor_javascript'] = implode("\n", $javascript);
    }

    /**
     *
     * @see \Symfony\Component\Form\AbstractType::getName()
     */
    public function getName()
    {
        return 'html_editor';
    }
}