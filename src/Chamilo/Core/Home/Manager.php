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
