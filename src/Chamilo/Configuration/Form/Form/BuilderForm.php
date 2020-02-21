<?php
namespace Chamilo\Configuration\Form\Form;

use Chamilo\Configuration\Form\Storage\DataClass\Element;
use Chamilo\Configuration\Form\Storage\DataClass\Option;
use Chamilo\Configuration\Form\Storage\DataManager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package configuration\form
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BuilderForm extends FormValidator
{

    private $user;

    private $form_type;

    private $element;
    const TYPE_CREATE = 0;
    const TYPE_EDIT = 1;

    public function __construct($form_type, $element, $action, $user)
    {
        parent::__construct('dynamic_form_element', 'post', $action);
        $this->user = $user;
        $this->form_type = $form_type;
        $this->element = $element;

        if ($form_type == self::TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        else
        {
            $this->build_edit_form();
        }
    }

    public function build_creation_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation::get('Create', null, Utilities::COMMON_LIBRARIES));
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_edit_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation::get('Update', null, Utilities::COMMON_LIBRARIES));
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_basic_form()
    {
        $this->addElement('category', $this->element->get_type_name($this->element->get_type()));

        $this->addElement('text', Element::PROPERTY_NAME, Translation::get('Name'));
        $this->addRule(
            Element::PROPERTY_NAME,
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
            'required');

        $this->addElement(
            'checkbox',
            Element::PROPERTY_REQUIRED,
            Translation::get('Required', null, Utilities::COMMON_LIBRARIES));

        $this->addElement('category');

        if ($this->element->get_type() >= Element::TYPE_RADIO_BUTTONS)
        {
            $this->build_options();
        }

        $this->setDefaults();
    }

    public function build_options()
    {
        $this->addElement('category', Translation::get('Options'));

        if (! $this->isSubmitted())
        {
            unset($_SESSION['mc_number_of_options']);
            unset($_SESSION['mc_skip_options']);

            if (count($this->element->get_options()) > 0)
            {
                $_SESSION['mc_number_of_options'] = count($this->element->get_options());
            }
        }

        if (! isset($_SESSION['mc_number_of_options']))
        {
            $_SESSION['mc_number_of_options'] = 3;
        }

        if (! isset($_SESSION['mc_skip_options']))
        {
            $_SESSION['mc_skip_options'] = array();
        }

        if (isset($_POST['add']))
        {
            $_SESSION['mc_number_of_options'] = $_SESSION['mc_number_of_options'] + 1;
        }
        if (isset($_POST['remove']))
        {
            $indexes = array_keys($_POST['remove']);
            $_SESSION['mc_skip_options'][] = $indexes[0];
        }

        $number_of_options = intval($_SESSION['mc_number_of_options']);

        for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (! in_array($option_number, $_SESSION['mc_skip_options']))
            {
                $group = array();
                $group[] = $this->createElement(
                    'text',
                    'option_' . Option::PROPERTY_NAME . '[' . $option_number . ']',
                    Translation::get('Name'),
                    array("size" => "50"));
                if ($number_of_options - count($_SESSION['mc_skip_options']) > 1)
                {
                    $group[] = $this->createElement(
                        'image',
                        'remove[' . $option_number . ']',
                        Theme::getInstance()->getCommonImagePath('Action/ListRemove'),
                        array('style="border: 0px;"'));
                }
                $this->addGroup(
                    $group,
                    'option_' . Option::PROPERTY_NAME . '[' . $option_number . ']',
                    Translation::get('OptionName'),
                    '',
                    false);
                $this->addRule(
                    'option_' . Option::PROPERTY_NAME . '[' . $option_number . ']',
                    Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
                    'required');
            }
        }

        $this->addElement(
            'image',
            'add[]',
            Theme::getInstance()->getCommonImagePath('Action/ListAdd'),
            array('style="border: 0px;"'));

        $this->addElement('category');
    }

    public function validate()
    {
        if (isset($_POST['add']) || isset($_POST['remove']))
        {
            return false;
        }
        return parent::validate();
    }

    public function create_dynamic_form_element()
    {
        $values = $this->exportValues();
        $element = $this->element;

        $element->set_name($values[Element::PROPERTY_NAME]);
        $element->set_required($values[Element::PROPERTY_REQUIRED] ? 1 : 0);
        $succes = $element->create();

        if (! $succes)
            return false;

        foreach ($values['option_' . Option::PROPERTY_NAME] as $option)
        {
            $element_option = new Option();
            $element_option->set_dynamic_form_element_id($element->get_id());
            $element_option->set_name($option);
            $succes &= $element_option->create();
        }

        return $succes;
    }

    public function update_dynamic_form_element()
    {
        $values = $this->exportValues();
        $element = $this->element;

        $element->set_name($values[Element::PROPERTY_NAME]);
        $element->set_required($values[Element::PROPERTY_REQUIRED] ? 1 : 0);
        $succes = $element->update();

        if (! $succes)
            return false;

        DataManager::delete_all_options_from_form_element($element->get_id());

        foreach ($values['option_' . Option::PROPERTY_NAME] as $option)
        {
            $element_option = new Option();
            $element_option->set_dynamic_form_element_id($element->get_id());
            $element_option->set_name($option);
            $succes &= $element_option->create();
        }

        return $succes;
    }

    public function setDefaults($parameters = array())
    {
        $parameters[Element::PROPERTY_NAME] = $this->element->get_name();
        $parameters[Element::PROPERTY_REQUIRED] = $this->element->get_required();

        if (! $this->isSubmitted())
        {
            $element = $this->element;
            if (! is_null($element))
            {
                $options = $element->get_options();

                foreach ($options as $index => $option)
                {
                    $parameters['option_' . Option::PROPERTY_NAME][$index] = $option->get_name();
                }
            }
        }

        parent::setDefaults($parameters);
    }
}
