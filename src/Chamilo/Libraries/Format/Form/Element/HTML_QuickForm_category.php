<?php
/**
 *
 * @package common.html.formvalidator.Element
 */

/**
 * A pseudo-element used for adding raw HTML to form
 * Intended for use with the default renderer only, template-based
 * ones may (and probably will) completely ignore this
 *
 * @author Alexey Borzov <borz_off@cs.msu.su>
 * @access public
 */
class HTML_QuickForm_category extends HTML_QuickForm_html
{

    // {{{ constructor

    /**
     * Class constructor
     *
     * @param string $text raw HTML to add
     * @access public
     * @return void
     */
    public function __construct($title = null, $extra_classes = null)
    {
        $html = array();

        if ($title != null)
        {
            // $html[] = '<div class="configuration_form' . ($extra_classes ? ' ' . $extra_classes : '') . '" >';
            $html[] = '<h4 class="form-category">' . $title . '</h4>';
        }
        else
        {
            // $html[] = '<div style="clear: both;"></div>';
            // $html[] = '</div>';
        }

        $html = implode(PHP_EOL, $html);

        parent::__construct($html);
    }
} //end class HTML_QuickForm_header
