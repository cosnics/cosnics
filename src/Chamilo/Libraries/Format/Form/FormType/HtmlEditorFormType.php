<?php
namespace Chamilo\Libraries\Format\Form\FormType;

use Chamilo\Libraries\Format\Form\FormValidatorHtmlEditor;
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
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @param array $options
     *
     * @see \Symfony\Component\Form\AbstractType::buildView()
     *
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $label = !empty($view->vars['label']) ? $view->vars['label'] : Translation::get($view->vars['name']);

        $html_editor = new FormValidatorHtmlEditor(
            $view->vars['full_name'], $label, false, $options['html_editor_options'], $options['html_editor_attributes']
        );

        $javascript = [];

        $includes = $html_editor->get_includes();
        foreach ($includes as $include)
        {
            if (!empty($include))
            {
                $javascript[] = $include;
            }
        }

        $javascript = array_merge($javascript, $html_editor->get_javascript());

        $view->vars['html_editor_javascript'] = implode(PHP_EOL, $javascript);
    }

    /**
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @see \Symfony\Component\Form\AbstractType::setDefaultOptions()
     *
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'html_editor_options' => [],
                'html_editor_attributes' => [],
                'compound' => false
            ]
        );

        $resolver->setAllowedTypes('html_editor_options', array('array'));
        $resolver->setAllowedTypes('html_editor_attributes', array('array'));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'html_editor';
    }
}