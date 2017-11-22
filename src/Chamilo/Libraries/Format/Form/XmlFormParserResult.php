<?php
namespace Chamilo\Libraries\Format\Form;

/**
 * This class holds the result of the xml form parser.
 * The result includes the elements, the validation rules and the default values.
 *
 * @package Chamilo\Libraries\Format\Form
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class XmlFormParserResult
{

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */

    /**
     * An array of the elements
     *
     * @var \HTML_QuickForm_element[]
     */
    private $elements;

    /**
     * An array of the validation rules
     *
     * @var \Chamilo\Libraries\Format\Form\XmlFormParserValidationRule
     */
    private $validation_rules;

    /**
     * An array of the default values
     *
     * @var string[]
     */
    private $default_values;

    /**
     * Constructor
     * Initializes the form builder
     */
    public function __construct()
    {
        $this->clear_elements();
        $this->clear_default_values();
        $this->clear_validation_rules();
    }

    /**
     * Adds this result to a given form
     *
     * @param \Chamilo\Libraries\Format\Form\FormValidator $form
     */
    public function add_result_to_form(FormValidator $form)
    {
        foreach ($this->get_elements() as $element)
        {
            $this->add_element_fo_form($form, $element);
        }

        $form->setDefaults(array_merge($form->_defaultValues, $this->get_default_values()));

        foreach ($this->get_validation_rules() as $validation_rule)
        {
            $validation_rule->add_to_form($form);
        }
    }

    /**
     * Adds a given element to the form
     *
     * @param \Chamilo\Libraries\Format\Form\FormValidator $form
     * @param HTML_QuickForm_element
     */
    protected function add_element_fo_form(FormValidator $form, $element)
    {
        $form->addElement($element);
    }

    /**
     * Adds an element to the elements list
     *
     * @param \HTML_QuickForm_element $element
     */
    public function add_element($element)
    {
        $this->elements[] = $element;
    }

    /**
     * Clears the elements list
     */
    public function clear_elements()
    {
        $this->set_elements(array());
    }

    /**
     * Returns wheter or not the result has elements
     *
     * @return boolean
     */
    public function has_elements()
    {
        return count($this->get_elements() > 0);
    }

    /**
     * Adds an default value for an element to the default_values list
     *
     * @param string $elementName
     * @param string $defaultValue
     */
    public function add_default_value($elementName, $defaultValue)
    {
        $this->default_values[$elementName] = $defaultValue;
    }

    /**
     * Clears the default_values list
     */
    public function clear_default_values()
    {
        $this->set_default_values(array());
    }

    /**
     * Returns wheter or not the result has default_values
     *
     * @return boolean
     */
    public function has_default_values()
    {
        return count($this->get_default_values() > 0);
    }

    /**
     * Adds an validation_rule to the validation_rules list
     *
     * @param \\Chamilo\Libraries\Format\Form\XmlFormParserValidationRule $validationRule
     */
    public function add_validation_rule($validationRule)
    {
        $this->validation_rules[] = $validationRule;
    }

    /**
     * Clears the validation_rules list
     */
    public function clear_validation_rules()
    {
        $this->set_validation_rules(array());
    }

    /**
     * Returns wheter or not the result has validation_rules
     *
     * @return boolean
     */
    public function has_validation_rules()
    {
        return count($this->get_validation_rules() > 0);
    }

    /**
     * Returns the elements
     *
     * @return \HTML_QuickForm_element[]
     */
    public function get_elements()
    {
        return $this->elements;
    }

    /**
     * Sets the elements
     *
     * @param \HTML_QuickForm_element[] $elements
     */
    public function set_elements($elements)
    {
        $this->elements = $elements;
    }

    /**
     * Returns the default_values
     *
     * @return string[]
     */
    public function get_default_values()
    {
        return $this->default_values;
    }

    /**
     * Sets the default_values
     *
     * @param string[] $defaultValues
     */
    public function set_default_values($defaultValues)
    {
        $this->default_values = $defaultValues;
    }

    /**
     * Returns the validation_rules
     *
     * @return \Chamilo\Libraries\Format\Form\XmlFormParserValidationRule[]
     */
    public function get_validation_rules()
    {
        return $this->validation_rules;
    }

    /**
     * Sets the validation_rules
     *
     * @param \\Chamilo\Libraries\Format\Form\XmlFormParserValidationRule[] $validationRules
     */
    public function set_validation_rules($validationRules)
    {
        $this->validation_rules = $validationRules;
    }
}
