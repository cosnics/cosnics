<?php

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Format\Form\Element
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class HTML_QuickForm_toggle extends HTML_QuickForm_extended_checkbox
{

    /**
     *
     * @param string $elementName
     * @param string $elementLabel
     * @param string $text
     * @param string[] $attributes
     * @param integer $value
     * @param string $return_value
     */
    public function __construct(
        $elementName = null, $elementLabel = null, $text = '', $attributes = null, $value = 1, $return_value = null
    )
    {
        parent::__construct($elementName, $elementLabel, $text, $attributes, $value, $return_value);
        $this->_type = 'toggle';
    }

    /**
     *
     * @see HTML_QuickForm_extended_checkbox::getCheckboxClasses()
     */
    function getCheckboxClasses()
    {
        return 'checkbox no-awesome-style';
    }

    /**
     * @return string
     */
    function toHtml()
    {
        $html = [];

        $html[] = parent::toHtml();
        $html[] = ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath(Utilities::COMMON_LIBRARIES, true) . 'Toggle.js'
        );

        return implode(PHP_EOL, $html);
    }
}
