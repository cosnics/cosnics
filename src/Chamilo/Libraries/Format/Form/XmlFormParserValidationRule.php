<?php
namespace Chamilo\Libraries\Format\Form;

/**
 * This class holds a validation rule untill it can be used into a form.
 * We need this because quickform does not
 * support storing validation rules and reusing them in another form
 *
 * @package Chamilo\Libraries\Format\Form
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class XmlFormParserValidationRule
{

    /**
     * The form element name
     *
     * @var string
     */
    private $element_name;

    /**
     * The message to display
     *
     * @var string
     */
    private $message;

    /**
     * The validation rule type
     *
     * @var string
     */
    private $type;

    /**
     * The format - Required for extra rule data
     *
     * @var string
     */
    private $format;

    /**
     * The place to execute the validation
     * Server - Client
     *
     * @var string
     */
    private $validation;

    /**
     * Whether or not to reset the elements on client validation error
     *
     * @var boolean
     */
    private $reset;

    /**
     * Forces the rule to be applied, even if the element does not exist yet
     *
     * @var boolean
     */
    private $force;

    /**
     * Constructor
     *
     * @param string $element - The form element name
     * @param string $message - The message to display
     * @param string $type - The validation rule type
     * @param string $format - [OPTIONAL] The format - Required for extra rule data
     * @param string $validation - [OPTIONAL] The place to execute the validation Server - Client
     * @param boolean $reset - [OPTIONAL] Whether or not to reset the elements on client validation error
     * @param boolean $force - [OPTIONAL] Forces the rule to be applied, even if the element does not exist yet
     */
    public function __construct($element, $message, $type, $format = null, $validation = 'server', $reset = false, $force = false)
    {
        $this->set_element_name($element);
        $this->set_message($message);
        $this->setType($type);
        $this->set_format($format);
        $this->set_validation($validation);
        $this->set_reset($reset);
        $this->set_force($force);
    }

    /**
     * Adds this validation rule to a given form
     *
     * @param \Chamilo\Libraries\Format\Form\FormValidator $form
     */
    public function add_to_form(FormValidator $form)
    {
        $form->addRule(
            $this->get_element_name(),
            $this->get_message(),
            $this->getType(),
            $this->get_format(),
            $this->get_validation(),
            $this->get_reset(),
            $this->get_force());
    }

    /**
     * Returns the element_name property of this object
     *
     * @return string
     */
    public function get_element_name()
    {
        return $this->element_name;
    }

    /**
     * Sets the element_name property of this object
     *
     * @param string $elementName
     */
    public function set_element_name($elementName)
    {
        $this->element_name = $elementName;
    }

    /**
     * Returns the message property of this object
     *
     * @return string
     */
    public function get_message()
    {
        return $this->message;
    }

    /**
     * Sets the message property of this object
     *
     * @param string $message
     */
    public function set_message($message)
    {
        $this->message = $message;
    }

    /**
     * Returns the type property of this object
     *
     * @deprecated Use XmlFormParserValidationRule::getType() now
     */
    public function get_type()
    {
        return $this->getType();
    }

    /**
     * Returns the type property of this object
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type property of this object
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @deprecated Use XmlFormParserValidationRule::setType() now
     */
    public function set_type($type)
    {
        $this->setType($type);
    }

    /**
     * Returns the format property of this object
     *
     * @return string
     */
    public function get_format()
    {
        return $this->format;
    }

    /**
     * Sets the format property of this object
     *
     * @param string $format
     */
    public function set_format($format)
    {
        $this->format = $format;
    }

    /**
     * Returns the validation property of this object
     *
     * @return string
     */
    public function get_validation()
    {
        return $this->validation;
    }

    /**
     * Sets the validation property of this object
     *
     * @param string $validation
     */
    public function set_validation($validation)
    {
        $this->validation = $validation;
    }

    /**
     * Returns the reset property of this object
     *
     * @return boolean
     */
    public function get_reset()
    {
        return $this->reset;
    }

    /**
     * Sets the reset property of this object
     *
     * @param boolean $reset
     */
    public function set_reset($reset)
    {
        $this->reset = $reset;
    }

    /**
     * Returns the force property of this object
     *
     * @return boolean
     */
    public function get_force()
    {
        return $this->force;
    }

    /**
     * Sets the force property of this object
     *
     * @param boolean $force
     */
    public function set_force($force)
    {
        $this->force = $force;
    }
}
