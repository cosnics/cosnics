<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * HTML class for a radio type element
 * PHP versions 4 and 5
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 * 
 * @category HTML
 * @package HTML_QuickForm
 * @author Adam Daniel <adaniel1@eesus.jnj.com>
 * @author Bertrand Mansion <bmansion@mamasam.com>
 * @copyright 2001-2011 The PHP Group
 * @license http://www.php.net/license/3_01.txt PHP License 3.01
 * @version CVS: $Id$
 * @link http://pear.php.net/package/HTML_QuickForm
 */

/**
 * Base class for <input /> form elements
 */
/**
 * HTML class for a radio type element
 * 
 * @category HTML
 * @package HTML_QuickForm
 * @author Adam Daniel <adaniel1@eesus.jnj.com>
 * @author Bertrand Mansion <bmansion@mamasam.com>
 * @version Release: 3.2.14
 * @since 1.0
 */
class HTML_QuickForm_bootstrap_radio extends HTML_QuickForm_radio
{

    /**
     * Class constructor
     * 
     * @param string Input field name attribute
     * @param mixed Label(s) for a field
     * @param string Text to display near the radio
     * @param string Input field value
     * @param mixed Either a typical HTML attribute string or an associative array
     * @since 1.0
     * @access public
     * @return void
     */
    function __construct($elementName = null, $elementLabel = null, $text = null, $value = null, $attributes = null)
    {
        parent::__construct($elementName, $elementLabel, $text, $value, $attributes);
    }

    /**
     * Returns the radio element in HTML
     * 
     * @since 1.0
     * @access public
     * @return string
     */
    function toHtml()
    {
        if(!$this->isFrozen())
        {
            $html = array();

            $html[] = '<div class="radio">';
            $html[] = HTML_QuickForm_input::toHtml();
            $html[] = '<label>';
            $html[] = $this->_text;
            $html[] = '</label>';
            $html[] = '</div>';

            return implode(PHP_EOL, $html);
        }

        return '<div>' . parent::toHtml() . '</div>';
    }
}
