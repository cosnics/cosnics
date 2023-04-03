<?php

namespace Chamilo\Libraries\Format\Form\Element;

use HTML_QuickForm_radio;

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
 * @package Chamilo\Libraries\Format\Form\Element
 * @author Adam Daniel <adaniel1@eesus.jnj.com>
 * @author Bertrand Mansion <bmansion@mamasam.com>
 * @version Release: 3.2.14
 * @since 1.0
 */
class HTML_QuickForm_bootstrap_radio extends HTML_QuickForm_radio
{

    /**
     *
     * @param string $elementName
     * @param string $elementLabel
     * @param string $text
     * @param string $value
     * @param string[] $attributes
     */
    function __construct($elementName = null, $elementLabel = null, $text = null, $value = null, $attributes = null)
    {
        parent::__construct($elementName, $elementLabel, $text, $value, $attributes);
    }

    /**
     *
     * @see HTML_QuickForm_radio::toHtml()
     */
    function toHtml(): string
    {
        if (! $this->isFrozen())
        {
            $html = array();

            $html[] = '<div class="radio">';
            $html[] = \HTML_QuickForm_input::toHtml();
            $html[] = '<label>';
            $html[] = $this->_text;
            $html[] = '</label>';
            $html[] = '</div>';

            return implode(PHP_EOL, $html);
        }

        return '<div>' . parent::toHtml() . '</div>';
    }
}
