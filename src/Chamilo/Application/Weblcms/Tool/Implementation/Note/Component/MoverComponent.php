<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Note\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Note\Manager;
use Chamilo\Libraries\Platform\Session\Request;

class MoverComponent extends Manager
{

    public function get_move_direction()
    {
        return Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_MOVE_DIRECTION);
    }
}
