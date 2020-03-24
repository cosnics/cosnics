<?php

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 *
 * @package Chamilo\Libraries\Format\Form\Element
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class HTML_QuickForm_stylebutton extends HTML_QuickForm_element
{

    /**
     *
     * @var string
     */
    private $styleButtonLabel;

    /**
     *
     * @var string
     */
    private $glyph;

    /**
     *
     * @param string $elementName
     * @param string $elementLabel
     * @param string[] $attributes
     * @param string $value
     * @param \Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph $glyph
     */
    public function __construct(
        $elementName = null, $elementLabel = null, $attributes = null, $value = null, InlineGlyph $glyph = null
    )
    {
        HTML_QuickForm_element::__construct($elementName, null, $attributes);

        $defaultAttributes = array();
        $defaultAttributes[] = 'btn';
        $defaultAttributes[] = 'btn-default';
        $defaultAttributes[] = $this->getAttribute('class');

        $this->setAttribute('class', implode(' ', $defaultAttributes));

        $this->styleButtonLabel = $elementLabel;
        $this->glyph = $glyph;

        if (isset($value))
        {
            $this->setValue($value);
        }
        else
        {
            $this->setValue($elementLabel);
        }
    }

    /**
     *
     * @see HTML_QuickForm_element::exportValue()
     */
    public function exportValue(&$submitValues, $assoc = false)
    {
        $type = $this->getType();
        if ('reset' == $type || 'button' == $type)
        {
            return null;
        }
        else
        {
            return parent::exportValue($submitValues, $assoc);
        }
    }

    /**
     *
     * @see HTML_QuickForm_element::getFrozenHtml()
     */
    function getFrozenHtml()
    {
        return '';
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph
     */
    public function getGlyph()
    {
        return $this->glyph;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph $glyph
     */
    public function setGlyph(InlineGlyph $glyph = null)
    {
        $this->glyph = $glyph;
    }

    /**
     *
     * @see HTML_QuickForm_element::getName()
     */
    public function getName()
    {
        return $this->getAttribute('name');
    }

    /**
     *
     * @return string
     */
    public function getStyleButtonLabel()
    {
        return $this->styleButtonLabel;
    }

    /**
     *
     * @see HTML_QuickForm_element::getValue()
     */
    public function getValue()
    {
        return $this->getAttribute('value');
    }

    /**
     *
     * @see HTML_QuickForm_element::onQuickFormEvent()
     */
    public function onQuickFormEvent($event, $arg, &$caller)
    {
        // do not use submit values for button-type elements
        $type = $this->getType();
        if (('updateValue' != $event) || ('submit' != $type && 'reset' != $type && 'button' != $type))
        {
            parent::onQuickFormEvent($event, $arg, $caller);
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

    /**
     *
     * @see HTML_QuickForm_element::setName()
     */
    public function setName($name)
    {
        $this->updateAttributes(array('name' => $name));
    }

    /**
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->_type = $type;
        $this->updateAttributes(array('type' => $type));
    }

    /**
     *
     * @see HTML_QuickForm_element::setValue()
     */
    public function setValue($value)
    {
        $this->updateAttributes(array('value' => $value));
    }

    /**
     *
     * @see HTML_Common::toHtml()
     */
    public function toHtml()
    {
        if ($this->_flagFrozen)
        {
            return $this->getFrozenHtml();
        }
        else
        {
            $html = array();

            $html[] = $this->_getTabs() . '<button' . $this->_getAttrString($this->_attributes) . ' >';

            if ($this->getGlyph())
            {
                $html[] =
                    $this->_getTabs() . $this->getGlyph()->render() . ($this->getStyleButtonLabel() ? '&nbsp;' : '');
            }

            if ($this->getStyleButtonLabel())
            {
                $html[] = $this->_getTabs() . $this->getStyleButtonLabel();
            }

            $html[] = $this->_getTabs() . '</button>';

            return implode(PHP_EOL, $html);
        }
    }
}
