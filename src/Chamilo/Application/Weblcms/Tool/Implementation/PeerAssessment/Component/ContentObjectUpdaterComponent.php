<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\PeerAssessment\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\PeerAssessment\Manager;

class ContentObjectUpdaterComponent extends Manager
{

    function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
    }
}