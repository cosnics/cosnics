<?php
namespace Chamilo\Core\Admin;

use Chamilo\Core\Admin\Architecture\Enum\ActionEnum;
use Chamilo\Libraries\Architecture\Domain\Application;

/**
 * @package Chamilo\Core\Admin
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Dieter De Neef
 */
abstract class Manager extends Application
{
    public const CONTEXT = __NAMESPACE__;
    public const PARAM_SELECTED_CONTEXT = 'context';
    public const PARAM_USER_ID = 'user_id';

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
        return ActionEnum::BROWSE->value;
    }
}
