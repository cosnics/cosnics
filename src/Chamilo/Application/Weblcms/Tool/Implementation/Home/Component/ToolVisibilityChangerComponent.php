<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Home\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Home\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class ToolVisibilityChangerComponent extends Manager
{
    // TODO: change this to new course tool structure
    public function run()
    {
        $tool = Request :: get(self :: PARAM_TOOL);
        $visibility = Request :: get(self :: PARAM_VISIBILITY);
        
        $success = DataManager :: set_tool_visibility_by_tool_name($this->get_course_id(), $tool, $visibility);
        
        $message = $success ? 'ToolVisibilityChanged' : 'ToolVisibilityNotChanged';
        
        $this->redirect(Translation :: get($message), ! $success, array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
    }
}
