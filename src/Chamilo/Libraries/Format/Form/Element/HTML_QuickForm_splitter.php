<?php
/**
 * $Id: category.php 128 2009-11-09 13:13:20Z vanpouckesven $
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
class HTML_QuickForm_splitter extends HTML_QuickForm_html
{
    
    // {{{ constructor
    
    /**
     * Class constructor
     * 
     * @param string $text raw HTML to add
     * @access public
     * @return void
     */
    public function HTML_QuickForm_splitter($title = null, $extra_classes = null)
    {
        $html = array();
        
        if ($title != null)
        {
            $html[] = '<div class="form_splitter' . ($extra_classes ? ' ' . $extra_classes : '') . '" >';
            $html[] = '<span class="category">' . $title . '</span>';
            $html[] = '<div style="clear: both;"></div>';
            $html[] = '</div>';
        }
        else
        {
            $html[] = '<div class="form_splitter' . ($extra_classes ? ' ' . $extra_classes : '') . '" >';
            $html[] = '<div style="clear: both;"></div>';
            $html[] = '</div>';
        }
        
        $html = implode("\n", $html);
        
        parent :: __construct($html);
    }
} //end class HTML_QuickForm_header
