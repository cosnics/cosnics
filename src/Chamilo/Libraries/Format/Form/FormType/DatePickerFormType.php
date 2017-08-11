<?php

namespace Chamilo\Libraries\Format\Form\FormType;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Format\Form\DataTransformer\DatePickerDataTransformer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form type to declare a date field with use of a jquery date picker
 *
 * @package common\libraries
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DatePickerFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new DatePickerDataTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'compound' => false,
                'data_class' => null,
                'data' => time()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $translation_variables = array('current_time', 'close', 'time', 'hour', 'minute');
        $translations = array();

        foreach($translation_variables as $translation_variable)
        {
            $translations[$translation_variable] = Translation::get(
                StringUtilities::getInstance()->createString($translation_variable)->upperCamelize()
            );
        }

        $translations['month_names'] = json_encode(DatetimeUtilities::get_month_long());
        $translations['day_names'] = json_encode(DatetimeUtilities::get_days_long());
        $translations['day_names_short'] = json_encode(DatetimeUtilities::get_days_short());

        $view->vars['translations'] = $translations;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'date_picker';
    }
}