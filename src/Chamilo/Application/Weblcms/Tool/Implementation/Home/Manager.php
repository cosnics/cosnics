<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Home;

use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements DelegateComponent
{
    const PARAM_TOOL = 'target_tool';

    const ACTION_CHANGE_TOOL_VISIBILITY = 'ToolVisibilityChanger';
    const ACTION_MAKE_TOOL_VISIBLE = 'ToolVisible';
    const ACTION_MAKE_TOOL_INVISIBLE = 'ToolInvisible';
    const ACTION_DELETE_LINKS = 'LinksDeleter';

    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
