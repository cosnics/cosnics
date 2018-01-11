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
class PercentInputFormType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'required' => true,
                'type' => 'integer'
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->setAttribute('required', $options['required']);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        // Use the Fully Qualified Class Name if the method getBlockPrefix exists.
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            return 'Symfony\Component\Form\Extension\Core\Type\PercentType';
        } else {
            return 'percent';
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
        return 'percent_input';
    }
}