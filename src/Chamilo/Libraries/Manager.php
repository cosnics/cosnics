<?php
namespace Chamilo\Libraries;

use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Architecture\Enum\ActionEnum;

/**
 * @package Chamilo\Libraries
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
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
        return ActionEnum::UTILITIES->value;
    }
}
