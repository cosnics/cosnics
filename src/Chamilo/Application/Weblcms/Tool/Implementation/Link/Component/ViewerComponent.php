<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Link\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Link\Manager;

/**
 * $Id: Link_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.tool.Link.component
 */
class ViewerComponent extends Manager
{

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }
}
