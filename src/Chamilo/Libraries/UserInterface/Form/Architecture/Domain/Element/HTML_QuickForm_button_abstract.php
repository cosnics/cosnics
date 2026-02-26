<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element;

use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;
use HTML_QuickForm;
use HTML_QuickForm_element;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class HTML_QuickForm_button_abstract extends HTML_QuickForm_element
{
    private ?InlineGlyph $glyph;

    private ?string $styleButtonLabel;

    public function __construct(
        ?string $elementName = null, ?string $elementLabel = null, null|array|string $attributes = null,
        ?string $value = null, ?InlineGlyph $glyph = null
    )
    {
        parent::__construct($elementName, null, $attributes);

        $defaultAttributes = [];
        $defaultAttributes[] = 'btn';
        $defaultAttributes[] = $this->getAttribute('class');

        $this->setAttribute('class', implode(' ', $defaultAttributes));

        $this->styleButtonLabel = $elementLabel;
        $this->glyph = $glyph;

        if (isset($value)) {
            $this->setValue($value);
        }
        else {
            $this->setValue($elementLabel);
        }
    }

    /**
     * Returns a 'safe' element's value
     */
    public function exportValue(array &$submitValues, bool $assoc = false): mixed
    {
        return $this->_prepareValue($this->_findValue($submitValues), $assoc);
    }

    public function getFrozenHtml(): string
    {
        return '';
    }

    public function getGlyph(): ?InlineGlyph
    {
        return $this->glyph;
    }

    public function getName(): string
    {
        return $this->getAttribute('name');
    }

    public function getStyleButtonLabel(): ?string
    {
        return $this->styleButtonLabel;
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
        // do not use submit values for button-type elements
        $type = $this->getType();

        if (('updateValue' != $event) || ('submit' != $type && 'reset' != $type && 'button' != $type)) {
            parent::onQuickFormEvent($event, $arg, $caller);
        }
        else {
            $value = $this->_findValue($caller->getConstantValues());

            if (null === $value) {
                $value = $this->_findValue($caller->getDefaultValues());
            }

            if (null !== $value) {
                $this->setValue($value);
            }
        }

        return true;
    }

    public function setName(string $name): void
    {
        $this->updateAttributes(['name' => $name]);
    }

    public function setType(string $type): void
    {
        $this->_type = $type;
        $this->updateAttributes(['type' => $type]);
    }

    public function setValue($value): void
    {
        $this->updateAttributes(['value' => $value]);
    }

    /**
     * @return string
     */
    public function toHtml(): string
    {
        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        }
        else {
            $html = [];

            $html[] = $this->_getTabs() . '<button' . $this->_getAttrString($this->_attributes) . ' >';

            if ($this->getGlyph() && $this->getStyleButtonLabel()) {
                $this->getGlyph()->addExtraClasses(['me-1']);
            }

            if ($this->getGlyph()) {
                $html[] = $this->_getTabs() . $this->getGlyph()->render();
            }

            if ($this->getStyleButtonLabel()) {
                $html[] = $this->_getTabs() . $this->getStyleButtonLabel();
            }

            $html[] = $this->_getTabs() . '</button>';

            return implode(PHP_EOL, $html);
        }
    }
}
