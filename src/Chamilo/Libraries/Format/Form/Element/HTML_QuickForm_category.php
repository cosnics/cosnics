<?php

/**
 * A pseudo-element used for adding raw HTML to form
 * Intended for use with the default renderer only, template-based
 * ones may (and probably will) completely ignore this
 *
 * @package Chamilo\Libraries\Format\Form\Element
 * @author Alexey Borzov <borz_off@cs.msu.su>
 * @access public
 */
class HTML_QuickForm_category extends HTML_QuickForm_html
{

    /**
     *
     * @param string $title
     * @param string $extra_classes
     */
    public function __construct($title = null, $extra_classes = null)
    {
        $html = array();

        if ($title != null)
        {
            $html[] = '<h4 class="form-category">' . $title . '</h4>';
        }

        $html = implode(PHP_EOL, $html);

        parent::__construct($html);
    }
}