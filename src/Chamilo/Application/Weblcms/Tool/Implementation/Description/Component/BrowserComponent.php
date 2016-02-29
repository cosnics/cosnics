<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Description\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Description\Manager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

class BrowserComponent extends Manager
{

    public function get_additional_parameters()
    {
        return array(self :: PARAM_BROWSE_PUBLICATION_TYPE);
    }

    /*
     * Inherited.
     */
    public function get_publication_count()
    {
        return count($this->publications);
    }

    public function render_header($visible_tools = null, $show_introduction_text = false)
    {
        $html = array();

        $html[] = parent :: render_header($visible_tools, $show_introduction_text);

        return implode(PHP_EOL, $html);
    }

    /**
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {

    }
}
