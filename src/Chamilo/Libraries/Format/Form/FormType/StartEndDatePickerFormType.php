<?php

namespace Chamilo\Libraries\Format\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form type to declare two date pickers, one with a start date, one with an end date. The date pickers are linked
 * to eachother so that the start date can never be bigger then the end date
 *
 * @package common\libraries
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class StartEndDatePickerFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('start_date', new DatePickerFormType());
        $builder->add('end_date', new DatePickerFormType());
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => null,
            )
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'start_end_date_picker';
    }
}