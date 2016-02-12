<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Blog\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Blog\Manager;

class BrowserComponent extends Manager
{

    public function get_additional_parameters()
    {
        return array(self :: PARAM_BROWSE_PUBLICATION_TYPE);
    }
}
