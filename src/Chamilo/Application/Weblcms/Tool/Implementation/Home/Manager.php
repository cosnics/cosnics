<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Home;

use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements DelegateComponent
{
    const PARAM_TOOL = 'target_tool';
    const ACTION_CHANGE_TOOL_VISIBILITY = 'tool_visibility_changer';
    const ACTION_MAKE_TOOL_VISIBLE = 'tool_visible';
    const ACTION_MAKE_TOOL_INVISIBLE = 'tool_invisible';
    const ACTION_DELETE_LINKS = 'links_deleter';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
