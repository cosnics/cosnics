<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Geolocation\Component;

use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Application\Weblcms\Tool\Implementation\Geolocation\Manager;

class MoverComponent extends Manager
{

    public function get_move_direction()
    {
        return Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_MOVE_DIRECTION);
    }
}
