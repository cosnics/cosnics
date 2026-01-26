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
class HTML_QuickForm_stylesubmitbutton extends HTML_QuickForm_stylebutton
{

    public function __construct(
        ?string $elementName = null, ?string $elementLabel = null, null|array|string $attributes = null,
        ?string $value = null, ?InlineGlyph $glyph = null
    )
    {
        // Quickform forces all arguments to "null", so the defaults in the constructor are not triggered
        if (!isset($glyph))
        {
            $glyph = new FontAwesomeGlyph('check', [], null, 'fas');
        }

        parent::__construct($elementName, $elementLabel, $attributes, $value, $glyph);

        $this->setType('submit');

        $defaultAttributes = [];
        $defaultAttributes[] = $this->getAttribute('class');
        $defaultAttributes[] = 'btn-success';

        $this->setAttribute('class', implode(' ', $defaultAttributes));
    }

    /**
     * Returns a 'safe' element's value
     */
    public function exportValue(array &$submitValues, bool $assoc = false): mixed
    {
        return $this->_prepareValue($this->_findValue($submitValues), $assoc);
    }
}
