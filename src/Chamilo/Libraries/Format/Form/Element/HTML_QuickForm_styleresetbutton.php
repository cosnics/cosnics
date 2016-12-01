<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'HTML_QuickForm_stylebutton.php';
/**
 *
 * @package HTML_QuickForm_styleresetbutton
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class HTML_QuickForm_styleresetbutton extends HTML_QuickForm_stylebutton
{

    /**
     *
     * @param string $elementName
     * @param string $elementLabel
     * @param string[] $attributes
     * @param string $value
     * @param string $glyph
     */
    public function __construct($elementName = null, $elementLabel = null, $attributes = null, $value = null, $glyph = 'trash')
    {
        // Quickform forces all arguments to "null", so the defaults in the constructor are not triggered
        if (! isset($glyph))
        {
            $glyph = 'trash';
        }
        
        HTML_QuickForm_stylebutton::__construct($elementName, $elementLabel, $attributes, $value, $glyph);
        
        $this->setType('reset');
        
        $defaultAttributes = array();
        $defaultAttributes[] = $this->getAttribute('class');
        
        $this->setAttribute('class', implode(' ', $defaultAttributes));
    }

    /**
     *
     * @see HTML_QuickForm_stylebutton::exportValue()
     */
    public function exportValue(&$submitValues, $assoc = false)
    {
        return $this->_prepareValue($this->_findValue($submitValues), $assoc);
    }
}
