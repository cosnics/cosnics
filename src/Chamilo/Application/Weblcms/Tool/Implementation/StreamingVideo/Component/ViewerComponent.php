<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\StreamingVideo\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\StreamingVideo\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

class ViewerComponent extends Manager
{

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
    }
}
