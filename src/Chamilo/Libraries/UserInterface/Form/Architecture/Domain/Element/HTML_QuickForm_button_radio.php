<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element;

use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use HTML_QuickForm;
use HTML_QuickForm_input;

/**
 * Base class for <input /> form elements
 * HTML class for a radio type element
 * PHP versions 4 and 5
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category HTML
 * @package  Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element
 * @author   Adam Daniel <adaniel1@eesus.jnj.com>
 * @author   Bertrand Mansion <bmansion@mamasam.com>
 */
class HTML_QuickForm_button_radio extends HTML_QuickForm_input
{
    protected mixed $checkedValue = null;

    protected array $options;

    public function __construct(
        ?string $elementName = null, ?string $elementLabel = null, array $options = [],
        null|array|string $attributes = null
    )
    {
        parent::__construct($elementName, $elementLabel, $attributes);

        $this->_persistantFreeze = true;
        $this->options = $options;
        $this->setType('radio');

        $defaultAttributes = [];
        $defaultAttributes[] = $this->getAttribute('class');
        $defaultAttributes[] = 'form-check-input';

        $this->setAttribute('class', implode(' ', $defaultAttributes));
    }

    public function getFrozenHtml(): string
    {
        $html = [];

        foreach ($this->options as $value => $name) {
            $html[] = '<div>';

            if ($value === $this->getValue()) {
                $glyph = new FontAwesomeGlyph('circle-check', ['fa-sm', 'me-1'], $name, 'fa-regular');
            }
            else {
                $glyph = new FontAwesomeGlyph('circle', ['opacity-25', 'fa-sm', 'me-1'], $name, 'fa-regular');
            }
            $html[] = $glyph->render();
            $html[] = '<label>' . $name . '</label>';
            $html[] = '</div>';
        }

        $html[] = $this->_getPersistantData();

        return implode(PHP_EOL, $html);
    }

    public function getValue(): mixed
    {
        return $this->checkedValue;
    }

    public function onQuickFormEvent(string $event, mixed $arg, ?HTML_QuickForm $caller = null): bool
    {
        switch ($event) {
            case 'updateValue':
                // constant values override both default and submitted ones
                // default values are overriden by submitted
                $value = $this->_findValue($caller->getConstantValues());

                if (null === $value) {
                    $value = $this->_findValue($caller->getSubmitValues());

                    if (null === $value) {
                        $value = $this->_findValue($caller->getDefaultValues());
                    }
                }

                $this->setValue($value);
                break;
            case 'setGroupValue':
                $this->setValue($arg);
                break;
            default:
                parent::onQuickFormEvent($event, $arg, $caller);
        }

        return true;
    }

    public function setValue($value): void
    {
        $this->checkedValue = $value;
    }

    public function toHtml(): string
    {
        if ($this->isFrozen()) {
            return $this->getFrozenHtml();
        }
        else {
            $html = [];

            foreach ($this->options as $value => $name) {
                $attributes = $this->_attributes;
                if ($value === $this->getValue()) {
                    $attributes['checked'] = 'true';
                }

                $html[] = '<div class="form-check">';
                $html[] =
                    $this->_getTabs() . '<input' . $this->_getAttrString($attributes) . ' value="' . $value . '" />';
                $html[] = '<label class="form-check-label">' . $name . '</label>';
                $html[] = '</div>';
            }

            return implode(PHP_EOL, $html);
        }
    }
}
