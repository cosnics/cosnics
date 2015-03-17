<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Geolocation\Component;

use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Application\Weblcms\Tool\Implementation\Geolocation\Manager;

class ShareContentObjectsComponent extends Manager implements DelegateComponent
{

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
    }
}
