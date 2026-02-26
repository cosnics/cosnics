<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element;

use HTML_QuickForm_html;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HTML_QuickForm_category extends HTML_QuickForm_html
{
    public function __construct(?string $title = null)
    {
        parent::__construct('<h4 class="form-category">' . $title . '</h4>');
    }
}