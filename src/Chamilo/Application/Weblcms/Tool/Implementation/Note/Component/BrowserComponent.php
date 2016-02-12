<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Note\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Note\Manager;

class BrowserComponent extends Manager
{

    public function get_additional_parameters()
    {
        return array(self :: PARAM_BROWSE_PUBLICATION_TYPE);
    }
}
