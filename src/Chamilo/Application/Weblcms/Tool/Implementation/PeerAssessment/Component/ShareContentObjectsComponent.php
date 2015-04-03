<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\PeerAssessment\Component;

use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Application\Weblcms\Tool\Implementation\PeerAssessment\Manager;

class ShareContentObjectsComponent extends Manager implements DelegateComponent
{

    function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
    }
}