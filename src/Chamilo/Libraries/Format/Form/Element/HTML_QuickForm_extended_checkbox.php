<?php
namespace Chamilo\Libraries\Format\Form\Element;

use HTML_QuickForm;
use HTML_QuickForm_checkbox;
use HTML_QuickForm_input;
use ReflectionClass;

/**
 * Extension on the HTML Quickform Checkbox element to support returnable values if the checkbox is not selected
 *
 * @package Chamilo\Libraries\Format\Form\Element
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class HTML_QuickForm_extended_checkbox extends HTML_QuickForm_checkbox
{

    /**
     * The return value if the checkbox is not selected
     */
    private ?string $return_value;

    /**
     * @param string $text               (optional)Checkbox display text
     * @param ?array|?string $attributes Associative array of tag attributes or HTML attributes name="value" pairs
     * @param int $value                 The value for the checkbox
     * @param ?string $return_value      The return value when the checkbox is not selected
     */
    public function __construct(
        ?string $elementName = null, ?string $elementLabel = null, string $text = '', $attributes = null,
        int $value = 1, ?string $return_value = null
    )
    {
        parent::__construct($elementName, $elementLabel, $text, $attributes);

        $this->setValue($value);

        $this->return_value = $return_value;
    }

    /**
     * Returns a 'safe' element's value
     */
    public function exportValue(array &$submitValues, bool $assoc = false)
    {
        $value = $this->_findValue($submitValues);

        if (null === $value)
        {
            $value = $this->getChecked() ? true : $this->return_value;
        }

        return $this->_prepareValue($value, $assoc);
    }

    public function getCheckboxClasses(): string
    {
        return 'checkbox no-toggle-style';
    }

    public function getReturnValue(): ?string
    {
        return $this->return_value;
    }

    public function setReturnValue(?string $return_value)
    {
        $this->return_value = $return_value;
    }

    public function getValue()
    {
        return $this->getAttribute('value');
    }

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param string $event            Name of event
     * @param mixed $arg               event arguments
     * @param ?\HTML_QuickForm $caller calling object
     */
    public function onQuickFormEvent(string $event, $arg, ?HTML_QuickForm $caller = null): bool
    {
        switch ($event)
        {
            case 'updateValue' :
                // constant values override both default and submitted ones
                // default values are overriden by submitted
                $value = $this->_findValue($caller->getConstantValues());
                if (null === $value)
                {
                    // if no boxes were checked, then there is no value in the array
                    // yet we don't want to display default value in this case
                    if ($caller->isSubmitted())
                    {
                        $value = $this->_findValue($caller->getSubmitValues());
                    }
                    else
                    {
                        $value = $this->_findValue($caller->getDefaultValues());
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

                if ('submit' != $type && 'reset' != $type && 'image' != $type && 'button' != $type)
                {
                    switch ($event)
                    {
                        case 'createElement' :
                            $class = new ReflectionClass($this);
                            $parameters = $class->getConstructor()->getParameters();

                            foreach ($parameters as $key => $parameter)
                            {
                                $arg[$key] = is_null($arg[$key]) ?
                                    ($parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null) :
                                    $arg[$key];
                            }

                            $this->__construct($arg[0], $arg[1], $arg[2], $arg[3], $arg[4], $arg[5]);
                            break;
                        case 'addElement' :
                            $this->onQuickFormEvent('createElement', $arg, $caller);
                            $this->onQuickFormEvent('updateValue', null, $caller);
                            break;
                    }

                    return true;
                }
                else
                {
                    $value = $this->_findValue($caller->getConstantValues());
                    if (null === $value)
                    {
                        $value = $this->_findValue($caller->getDefaultValues());
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

    public function setValue($value)
    {
        $this->updateAttributes(['value' => $value]);
    }

    public function toHtml(): string
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
