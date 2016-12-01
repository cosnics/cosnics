<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager;
use Chamilo\Libraries\Platform\Session\Request;

class MoverComponent extends Manager
{

    public function get_move_direction()
    {
        return Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_MOVE_DIRECTION);
    }
}
