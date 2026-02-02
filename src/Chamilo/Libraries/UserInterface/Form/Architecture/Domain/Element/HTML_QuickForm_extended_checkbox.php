<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element;

use HTML_QuickForm;
use HTML_QuickForm_input;
use ReflectionClass;

/**
 * Extension on the HTML Quickform Checkbox element to support returnable values if the checkbox is not selected
 *
 * @package Chamilo\Libraries\Format\Form\Element
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class HTML_QuickForm_extended_checkbox extends HTML_QuickForm_input
{
    protected string $_text = '';

    /**
     * The return value if the checkbox is not selected
     */
    private ?string $returnValue;

    /**
     * @param string $text (optional)Checkbox display text
     * @param ?array|?string $attributes Associative array of tag attributes or HTML attributes name="value" pairs
     * @param int $value The value for the checkbox
     * @param ?string $returnValue The return value when the checkbox is not selected
     */
    public function __construct(
        ?string $elementName = null, ?string $elementLabel = null, string $text = '',
        null|array|string $attributes = null, int $value = 1, ?string $returnValue = null
    )
    {
        parent::__construct($elementName, $elementLabel, $attributes);

        $this->_persistantFreeze = true;
        $this->_text = $text;
        $this->setType('checkbox');
        $this->updateAttributes(['value' => 1]);
        $this->setValue($value);

        $this->returnValue = $returnValue;
    }

    public function exportValue(array &$submitValues, bool $assoc = false): mixed
    {
        $value = $this->_findValue($submitValues);

        if (null === $value)
        {
            $value = $this->getChecked() ? true : $this->returnValue;
        }

        return $this->_prepareValue($value, $assoc);
    }

    public function getCheckboxClasses(): string
    {
        return 'checkbox no-toggle-style';
    }

    public function getChecked(): bool
    {
        return (bool) $this->getAttribute('checked');
    }

    public function getReturnValue(): ?string
    {
        return $this->returnValue;
    }

    public function setReturnValue(?string $returnValue): void
    {
        $this->returnValue = $returnValue;
    }

    public function getText(): string
    {
        return $this->_text;
    }

    public function setText(string $text): void
    {
        $this->_text = $text;
    }

    public function getValue(): ?string
    {
        return $this->getAttribute('value');
    }

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param string $event Name of event
     * @param mixed $arg event arguments
     * @param ?\HTML_QuickForm $caller calling object
     */
    public function onQuickFormEvent(string $event, mixed $arg, ?HTML_QuickForm $caller = null): bool
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

    public function setChecked(?bool $checked): void
    {
        if (!$checked)
        {
            $this->removeAttribute('checked');
        }
        else
        {
            $this->updateAttributes(['checked' => 'checked']);
        }
    }

    public function setValue($value): void
    {
        $this->updateAttributes(['value' => $value]);
    }

    public function toHtml(): string
    {
        if (!$this->isFrozen())
        {
            $html = [];

            $html[] = '<div class="' . $this->getCheckboxClasses() . '">';
            $html[] = parent::toHtml();
            $html[] = '<label>';
            $html[] = $this->_text;
            $html[] = '</label>';
            $html[] = '</div>';

            return implode(PHP_EOL, $html);
        }

        $this->_generateId(); // Seems to be necessary when this is used in a group.

        if (0 == strlen($this->_text))
        {
            $label = '';
        }
        elseif ($this->_flagFrozen)
        {
            $label = $this->_text;
        }
        else
        {
            $label = '<label for="' . $this->getAttribute('id') . '">' . $this->_text . '</label>';
        }

        return parent::toHtml() . $label;
    }
}
