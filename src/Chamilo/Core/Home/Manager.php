<?php
namespace Chamilo\Core\Home;

use Chamilo\Libraries\Architecture\Domain\Application;

/**
 * @package Chamilo\Core\Home
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_VIEW_HOME = 'Home';
    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_VIEW_HOME;
    public const PARAM_DIRECTION = 'direction';
    public const PARAM_HOME_ID = 'id';
    public const PARAM_HOME_TYPE = 'type';
    public const PARAM_OBJECT_ID = 'object_id';
    public const PARAM_PARENT_ID = 'parent_id';
    public const PARAM_RENDERER_TYPE = 'renderer_type';
    public const PARAM_TAB_ID = 'tab';
    public const TYPE_BLOCK = 'block';
    public const TYPE_COLUMN = 'column';
    public const TYPE_ROW = 'row';
    public const TYPE_TAB = 'tab';

    public function getContext(): string
    {
        return self::CONTEXT;
    }

    public function getDefaultAction(): string
    {
        return self::DEFAULT_ACTION;
    }
}
