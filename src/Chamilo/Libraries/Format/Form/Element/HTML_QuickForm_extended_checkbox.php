<?php

/**
 * Extension on the HTML Quickform Checkbox element to support returnable values if the checkbox is not selected
 *
 * @package Chamilo\Libraries\Format\Form\Element
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class HTML_QuickForm_extended_checkbox extends HTML_QuickForm_checkbox
{

    /**
     * The return value if the checkbox is not selected
     *
     * @var string
     */
    private $return_value;

    /**
     * Class constructor
     *
     * @param string $elementName (optional)Input field name attribute
     * @param string $elementLabel (optional)Input field value
     * @param string $text (optional)Checkbox display text
     * @param string[] $attributes (optional)Either a typical HTML attribute string
     *        or an associative array
     * @param integer $value (optional)The value for the checkbox
     * @param string $return_value (optional)The return value when the checkbox is not selected
     *
     * @return void
     * @since 1.0
     * @access public
     */
    public function __construct(
        $elementName = null, $elementLabel = null, $text = '', $attributes = null, $value = 1, $return_value = null
    )
    {
        HTML_QuickForm_checkbox::__construct($elementName, $elementLabel, $text, $attributes);

        if ($value && !is_null($value))
        {
            $this->setValue($value);
        }
        else
        {
            $this->setValue(1);
        }

        $this->return_value = $return_value;
    }

    /**
     * Returns a 'safe' element's value
     *
     * @param array   array of submitted values to search
     * @param bool    whether to return the value as associative array
     *
     * @access public
     * @return mixed
     */
    public function exportValue(&$submitValues, $assoc = false)
    {
        $value = $this->_findValue($submitValues);

        if (null === $value)
        {
            $value = $this->getChecked() ? true : $this->return_value;
        }

        return $this->_prepareValue($value, $assoc);
    }

    /**
     *
     * @return string
     */
    function getCheckboxClasses()
    {
        return 'checkbox no-toggle-style';
    }

    /**
     *
     * @return string
     */
    public function getReturnValue()
    {
        return $this->return_value;
    }

    /**
     *
     * @param string $return_value
     */
    public function setReturnValue($return_value)
    {
        $this->return_value = $return_value;
    }

    /**
     *
     * @see HTML_QuickForm_checkbox::getValue()
     */
    function getValue()
    {
        return $this->getAttribute('value');
    }

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param string $event Name of event
     * @param mixed $arg event arguments
     * @param object $caller calling object
     *
     * @return    boolean
     * @since     1.0
     * @access    public
     */
    function onQuickFormEvent($event, $arg, &$caller)
    {
        switch ($event)
        {
            case 'updateValue' :
                // constant values override both default and submitted ones
                // default values are overriden by submitted
                $value = $this->_findValue($caller->_constantValues);
                if (null === $value)
                {
                    // if no boxes were checked, then there is no value in the array
                    // yet we don't want to display default value in this case
                    if ($caller->isSubmitted())
                    {
                        $value = $this->_findValue($caller->_submitValues);
                    }
                    else
                    {
                        $value = $this->_findValue($caller->_defaultValues);
                    }
                }
                if (null !== $value || $caller->isSubmitted())
                {
                    $this->setChecked($value);
                }
                break;
            case 'setGroupValue' :
                $this->setChecked($arg);
                break;
            default :
                // do not use submit values for button-type elements
                $type = $this->getType();
                if (('updateValue' != $event) ||
                    ('submit' != $type && 'reset' != $type && 'image' != $type && 'button' != $type))
                {
                    switch ($event)
                    {
                        case 'createElement' :
                            $this->__construct($arg[0], $arg[1], $arg[2], $arg[3], $arg[4], $arg[5]);
                            break;
                        case 'addElement' :
                            $this->onQuickFormEvent('createElement', $arg, $caller);
                            $this->onQuickFormEvent('updateValue', null, $caller);
                            break;
                        case 'updateValue' :
                            // constant values override both default and submitted ones
                            // default values are overriden by submitted
                            $value = $this->_findValue($caller->_constantValues);
                            if (null === $value)
                            {
                                $value = $this->_findValue($caller->_submitValues);
                                if (null === $value)
                                {
                                    $value = $this->_findValue($caller->_defaultValues);
                                }
                            }
                            if (null !== $value)
                            {
                                $this->setValue($value);
                            }
                            break;
                        case 'setGroupValue' :
                            $this->setValue($arg);
                    }

                    return true;
                }
                else
                {
                    $value = $this->_findValue($caller->_constantValues);
                    if (null === $value)
                    {
                        $value = $this->_findValue($caller->_defaultValues);
                    }
                    if (null !== $value)
                    {
                        $this->setValue($value);
                    }
                }

                return true;
        }

        return true;
    }

    /**
     * Sets the value of the form element
     *
     * @param string $value Default value of the form element
     *
     * @return    void
     * @since     1.0
     * @access    public
     */
    function setValue($value)
    {
        $this->updateAttributes(array('value' => $value));
    }

    /**
     *
     * @see HTML_QuickForm_checkbox::toHtml()
     */
    function toHtml()
    {
        if (!$this->isFrozen())
        {
            $html = [];

            $html[] = '<div class="' . $this->getCheckboxClasses() . '">';
            $html[] = HTML_QuickForm_input::toHtml();
            $html[] = '<label>';
            $html[] = $this->_text;
            $html[] = '</label>';
            $html[] = '</div>';

            return implode(PHP_EOL, $html);
        }

        return parent::toHtml();
    }
}
