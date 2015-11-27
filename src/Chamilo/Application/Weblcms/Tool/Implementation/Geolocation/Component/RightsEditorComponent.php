<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Geolocation\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Application\Weblcms\Tool\Implementation\Geolocation\Manager;

class RightsEditorComponent extends Manager implements DelegateComponent
{

    public function get_available_rights($location)
    {
        return WeblcmsRights :: get_available_rights($location);
    }

    public function get_additional_parameters()
    {
        array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
    }
}
