<?php
namespace Chamilo\Configuration\Form\Form;

use Chamilo\Configuration\Form\Storage\DataClass\Element;
use Chamilo\Configuration\Form\Storage\DataClass\Value;
use Chamilo\Configuration\Form\Storage\DataManager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package configuration\form
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ExecuteForm extends FormValidator
{

    private $user;

    private $form;

    private $title;

    public function __construct($form, $action, $user, $title)
    {
        parent::__construct('dynamic_form_values', 'post', $action);
        $this->user = $user;
        $this->form = $form;
        $this->title = $title;
        
        $this->build_basic_form();
    }

    public function build_basic_form()
    {
        $elements = $this->form->get_elements();
        foreach ($elements as $element)
        {
            switch ($element->get_type())
            {
                case Element::TYPE_TEXTBOX :
                    $this->build_text_box($element);
                    break;
                case Element::TYPE_HTMLEDITOR :
                    $this->build_html_editor($element);
                    break;
                case Element::TYPE_CHECKBOX :
                    $this->build_checkbox($element);
                    break;
                case Element::TYPE_RADIO_BUTTONS :
                    $this->build_radio_buttons($element);
                    break;
                case Element::TYPE_SELECT_BOX :
                    $this->build_select_box($element);
                    break;
            }
        }
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Save', null, Utilities::COMMON_LIBRARIES));
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $this->setDefaults();
    }

    public function update_values()
    {
        $values = $this->exportValues();
        $succes = DataManager::delete_dynamic_form_element_values_from_form($this->form->get_id());
        
        if (! $succes)
            return false;
        
        foreach ($values['element'] as $element_id => $value)
        {
            $element_value = new Value();
            $element_value->set_dynamic_form_element_id($element_id);
            $element_value->set_user_id($this->user->get_id());
            
            if (is_array($value))
            {
                $value = $value[$element_id];
            }
            
            $element_value->set_value($value);
            $succes &= $element_value->create();
        }
        
        return $succes;
    }

    public function build_text_box($element)
    {
        $return = $this->addElement('text', 'element[' . $element->get_id() . ']', $element->get_name());
        if ($element->get_required())
        {
            $this->addRule(
                'element[' . $element->get_id() . ']', 
                Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
                'required');
        }
        
        return $return;
    }

    public function build_html_editor($element)
    {
        return $this->add_html_editor(
            'element[' . $element->get_id() . ']', 
            $element->get_name(), 
            $element->get_required());
    }

    public function build_checkbox($element)
    {
        $return = $this->addElement('checkbox', 'element[' . $element->get_id() . ']', $element->get_name());
        
        if ($element->get_required())
        {
            $this->addRule(
                'element[' . $element->get_id() . ']', 
                Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
                'required');
        }
        
        return $return;
    }

    public function build_radio_buttons($element)
    {
        $options = $element->get_options();
        
        $group = array();
        
        foreach ($options as $index => $option)
        {
            if ($index < count($options) - 1)
                $extra = '<br />';
            else
                $extra = '';
            
            $group[] = $this->createElement(
                'radio', 
                $element->get_id(), 
                null, 
                $option->get_name() . $extra, 
                $option->get_id());
        }
        
        $return = $this->addGroup($group, 'element[' . $element->get_id() . ']', $element->get_name(), '');
        
        if ($element->get_required())
        {
            $this->addRule(
                'element[' . $element->get_id() . ']', 
                Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
                'required');
        }
        
        return $return;
    }

    public function build_select_box($element)
    {
        $options = $element->get_options();
        
        foreach ($options as $option)
        {
            $new_options[$option->get_id()] = $option->get_name();
        }
        
        $return = $this->addElement('select', 'element[' . $element->get_id() . ']', $element->get_name(), $new_options);
        
        if ($element->get_required())
        {
            $this->addRule(
                'element[' . $element->get_id() . ']', 
                Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
                'required');
        }
        
        return $return;
    }

    public function setDefaults($parameters = array())
    {
        $subcondition = new EqualityCondition(
            new PropertyConditionVariable(Element::class_name(), Element::PROPERTY_DYNAMIC_FORM_ID), 
            new StaticConditionVariable($this->form->get_id()));
        $subselect = new SubselectCondition(
            new PropertyConditionVariable(Value::class_name(), Value::PROPERTY_DYNAMIC_FORM_ELEMENT_ID), 
            new PropertyConditionVariable(Element::class_name(), Element::PROPERTY_ID), 
            Element::get_table_name(), 
            $subcondition);
        
        $values = DataManager::retrieve_dynamic_form_element_values($subselect);
        
        while ($value = $values->next_result())
        {
            $element = DataManager::retrieve_dynamic_form_elements(
                new EqualityCondition(
                    new PropertyConditionVariable(Element::class_name(), Element::PROPERTY_ID), 
                    new StaticConditionVariable($value->get_dynamic_form_element_id())))->next_result();
            
            if ($element->get_type() == Element::TYPE_RADIO_BUTTONS)
            {
                $parameters['element[' . $value->get_dynamic_form_element_id() . '][' .
                     $value->get_dynamic_form_element_id() . ']'] = $value->get_value();
            }
            else
            {
                $parameters['element[' . $value->get_dynamic_form_element_id() . ']'] = $value->get_value();
            }
        }
        
        parent::setDefaults($parameters);
    }
}
