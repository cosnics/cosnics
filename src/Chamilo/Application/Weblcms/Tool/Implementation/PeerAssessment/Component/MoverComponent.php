<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\PeerAssessment\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\PeerAssessment\Manager;
use Chamilo\Libraries\Platform\Session\Request;

class MoverComponent extends Manager
{

    function get_move_direction()
    {
        return Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_MOVE_DIRECTION);
    }
}