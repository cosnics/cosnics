<?php
namespace Chamilo\Libraries\Format\Form\Element;

use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 * @package Chamilo\Libraries\Format\Form\Element
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class HTML_QuickForm_styleresetbutton extends HTML_QuickForm_stylebutton
{

    /**
     * @param ?string $elementName
     * @param ?string $elementLabel
     * @param ?array|?string $attributes Associative array of tag attributes or HTML attributes name="value" pairs
     * @param ?string $value
     * @param ?\Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph $glyph
     */
    public function __construct(
        ?string $elementName = null, ?string $elementLabel = null, $attributes = null, ?string $value = null,
        ?InlineGlyph $glyph = null
    )
    {
        // Quickform forces all arguments to "null", so the defaults in the constructor are not triggered
        if (!isset($glyph))
        {
            $glyph = new FontAwesomeGlyph('trash-alt');
        }

        parent::__construct($elementName, $elementLabel, $attributes, $value, $glyph);

        $this->setType('reset');

        $defaultAttributes = [];
        $defaultAttributes[] = $this->getAttribute('class');

        $this->setAttribute('class', implode(' ', $defaultAttributes));
    }

    /**
     * Returns a 'safe' element's value
     *
     * @param array $submitValues array of submitted values to search
     * @param bool $assoc         whether to return the value as associative array
     */
    public function exportValue(array &$submitValues, bool $assoc = false)
    {
        return $this->_prepareValue($this->_findValue($submitValues), $assoc);
    }
}
