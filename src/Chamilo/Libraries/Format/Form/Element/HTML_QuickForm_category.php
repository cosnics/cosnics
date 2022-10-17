<?php
namespace Chamilo\Libraries\Format\Form\Element;

use HTML_QuickForm_html;

/**
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HTML_QuickForm_category extends HTML_QuickForm_html
{

    public function __construct(?string $title = null)
    {
        $html = '';

        if ($title != null)
        {
            $html = '<h4 class="form-category">' . $title . '</h4>';
        }

        parent::__construct($html);
    }
}