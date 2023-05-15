<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Home;

use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
    implements DelegateComponent, IntroductionTextSupportInterface
{
    public const ACTION_CHANGE_TOOL_VISIBILITY = 'ToolVisibilityChanger';
    public const ACTION_DELETE_LINKS = 'LinksDeleter';
    public const ACTION_MAKE_TOOL_INVISIBLE = 'ToolInvisible';
    public const ACTION_MAKE_TOOL_VISIBLE = 'ToolVisible';

    public const CONTEXT = __NAMESPACE__;

    public const DEFAULT_ACTION = self::ACTION_BROWSE;
    public const PARAM_TOOL = 'target_tool';
}
