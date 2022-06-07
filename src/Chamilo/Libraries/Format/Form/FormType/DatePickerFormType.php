<?php
namespace Chamilo\Libraries\Format\Form\FormType;

use Chamilo\Libraries\Format\Form\DataTransformer\DatePickerDataTransformer;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type to declare a date field with use of a jquery date picker
 *
 * @package Chamilo\Libraries\Format\Form\FormType
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DatePickerFormType extends AbstractType
{

    /**
     *
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new DatePickerDataTransformer());
    }

    /**
     *
     * @see \Symfony\Component\Form\AbstractType::setDefaultOptions()
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('compound' => false, 'data_class' => null, 'data' => time()));
    }

    /**
     *
     * @see \Symfony\Component\Form\AbstractType::buildView()
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $translation_variables = array('current_time', 'close', 'time', 'hour', 'minute');
        $translations = [];

        foreach ($translation_variables as $translation_variable)
        {
            $translations[$translation_variable] = Translation::get(
                StringUtilities::getInstance()->createString($translation_variable)->upperCamelize());
        }

        $translations['month_names'] = json_encode(DatetimeUtilities::getInstance()->getMonthslong());
        $translations['day_names'] = json_encode(DatetimeUtilities::getInstance()->getDaysLong());
        $translations['day_names_short'] = json_encode(DatetimeUtilities::getInstance()->getDaysShort());

        $view->vars['translations'] = $translations;
    }

    /**
     *
     * @see \Symfony\Component\Form\AbstractType::getParent()
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     *
     * @see \Symfony\Component\Form\AbstractType::getName()
     */
    public function getName()
    {
        return 'date_picker';
    }
}