<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Wiki\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Wiki\Manager;

class ViewerComponent extends Manager
{

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }
}
