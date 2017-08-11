<?php

namespace Chamilo\Libraries\Format\Form\FormType;

use Chamilo\Libraries\Format\Form\FormValidatorHtmlEditor;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Translation;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form type to declare the html_editor form type
 *
 * @package common\libraries
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class HtmlEditorFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'html_editor_options' => array(),
                'html_editor_attributes' => array()
            )
        );

        $resolver->setAllowedTypes(
            array
            (
                'html_editor_options' => array('array'),
                'html_editor_attributes' => array('array')
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $label = !empty($view->vars['label']) ? $view->vars['label'] : Translation::get($view->vars['name']);

        $html_editor = FormValidatorHtmlEditor::factory(
            LocalSetting::get('html_editor'), $view->vars['full_name'], $label, false,
            $options['html_editor_options'], $options['html_editor_attributes']
        );

        $javascript = array();

        $includes = $html_editor->get_includes();
        foreach($includes as $include)
        {
            if(!empty($include))
            {
                $javascript[] = $include;
            }
        }

        $javascript = array_merge($javascript, $html_editor->get_javascript());

        $view->vars['html_editor_javascript'] = implode("\n", $javascript);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'html_editor';
    }
}