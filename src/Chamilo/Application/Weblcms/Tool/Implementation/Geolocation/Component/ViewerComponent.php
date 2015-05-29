<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Geolocation\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Geolocation\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

class ViewerComponent extends Manager implements DelegateComponent
{

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
    }
}
