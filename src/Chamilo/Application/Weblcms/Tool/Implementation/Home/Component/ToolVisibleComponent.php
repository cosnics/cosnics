<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Home\Component;

use Chamilo\Libraries\Platform\Session\Request;

class ToolVisibleComponent extends ToolVisibilityChangerComponent
{

    public function run()
    {
        Request::set_get(self::PARAM_VISIBILITY, 1);
        parent::run();
    }
}
