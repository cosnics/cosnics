<?php
namespace Chamilo\Core\Home;

use Chamilo\Core\Home\Architecture\Enum\ActionEnum;
use Chamilo\Libraries\Architecture\Domain\Application;

/**
 * @package Chamilo\Core\Home
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    public const CONTEXT = __NAMESPACE__;
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

    public function getApplicationAction(): string
    {
        return ActionEnum::getActionValue(static::class);
    }

    public function getApplicationContext(): string
    {
        return self::CONTEXT;
    }

    public function getDefaultApplicationAction(): string
    {
        return ActionEnum::VIEW_HOME->value;
    }
}
