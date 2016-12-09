<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\PeerAssessment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\PeerAssessment\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

class RightsEditorComponent extends Manager implements DelegateComponent
{

    function get_available_rights($location)
    {
        return WeblcmsRights :: get_available_rights($location);
    }

    function get_additional_parameters()
    {
        array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
    }
}