<?php
namespace Chamilo\Libraries;

use Chamilo\Libraries\Architecture\Domain\Application;

/**
 * @package Chamilo\Libraries
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_UTILITIES = 'Utilities';
    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_UTILITIES;

    public function getContext(): string
    {
        return self::CONTEXT;
    }

    public function getDefaultAction(): string
    {
        return self::DEFAULT_ACTION;
    }
}
