<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Home;

use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements DelegateComponent, IntroductionTextSupportInterface
{
    const PARAM_TOOL = 'target_tool';
    const ACTION_CHANGE_TOOL_VISIBILITY = 'ToolVisibilityChanger';
    const ACTION_MAKE_TOOL_VISIBLE = 'ToolVisible';
    const ACTION_MAKE_TOOL_INVISIBLE = 'ToolInvisible';
    const ACTION_DELETE_LINKS = 'LinksDeleter';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    /**
     *
     * @see \Chamilo\Application\Weblcms\Tool\Manager::render_header()
     */
    public function render_header($visible_tools = null, $show_introduction_text = false)
    {
        return Application :: render_header();
    }
}
