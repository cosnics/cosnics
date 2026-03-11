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
    public const string CONTEXT = __NAMESPACE__;
    public const string PARAM_DIRECTION = 'direction';
    public const string PARAM_HOME_ID = 'id';
    public const string PARAM_HOME_TYPE = 'type';
    public const string PARAM_OBJECT_ID = 'object_id';
    public const string PARAM_PARENT_ID = 'parent_id';
    public const string PARAM_RENDERER_TYPE = 'renderer_type';
    public const string PARAM_TAB_ID = 'tab';
    public const string TYPE_BLOCK = 'block';
    public const string TYPE_COLUMN = 'column';
    public const string TYPE_ROW = 'row';
    public const string TYPE_TAB = 'tab';

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
