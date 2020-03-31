<?php

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
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
        $html = '';

        if ($title != null)
        {
            $html = '<h4 class="form-category">' . $title . '</h4>';
        }

        parent::__construct($html);
    }
}