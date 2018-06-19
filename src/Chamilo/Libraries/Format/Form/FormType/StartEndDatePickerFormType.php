<?php
namespace Chamilo\Libraries\Format\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type to declare two date pickers, one with a start date, one with an end date.
 * The date pickers are linked
 * to eachother so that the start date can never be bigger then the end date
 *
 * @package Chamilo\Libraries\Format\Form\FormType
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class StartEndDatePickerFormType extends AbstractType
{

    /**
     *
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('start_date', new DatePickerFormType());
        $builder->add('end_date', new DatePickerFormType());
    }

    /**
     *
     * @see \Symfony\Component\Form\AbstractType::setDefaultOptions()
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => null));
    }

    /**
     *
     * @see \Symfony\Component\Form\AbstractType::getName()
     */
    public function getName()
    {
        return 'start_end_date_picker';
    }
}