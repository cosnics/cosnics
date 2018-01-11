<?php
namespace Chamilo\Libraries\Format\Form\FormType;

use Chamilo\Libraries\Format\Form\FormValidatorHtmlEditor;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Translation\Translation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CkeditorFormType
 * @package Chamilo\Libraries\Format\Form\FormType
 */
class HtmlEditorFormType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'html_editor_options' => array(),
                'html_editor_attributes' => array(),
                'label' => Translation::getInstance()->getTranslation('Invulveld'),
                'required' => true
            )
        );

        $resolver->setAllowedTypes('html_editor_options', ['array']);
        $resolver->setAllowedTypes('html_editor_attributes', ['array']);
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->setAttribute('html_editor_options', $options['html_editor_options'])
        ->setAttribute('html_editor_attributes', $options['html_editor_attributes'])
        ->setAttribute('required', $options['required'])
        ->setAttribute('label', $options['label']);
    }

    /**
     * @inheritdoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $html_editor = FormValidatorHtmlEditor::factory(
            LocalSetting::getInstance()->get('html_editor'),
            $view->vars['full_name'],
            $options['label'],
            $options['required'],
            $options['html_editor_options'],
            $options['html_editor_attributes']);

        //todo: should be in template?
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
     * {@inheritdoc}
     */
    public function getParent()
    {
        // Use the Fully Qualified Class Name if the method getBlockPrefix exists.
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            return 'Symfony\Component\Form\Extension\Core\Type\TextareaType';
        } else {
            return 'textarea';
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'html_editor';
    }
}