<?php
namespace Chamilo\Libraries;

use Chamilo\Libraries\Protocol\Ajax\Service\AjaxManager;

/**
 * @package Chamilo\Libraries\Ajax
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends AjaxManager
{
    public const ACTION_UTILITIES = 'Utilities';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_UTILITIES;
}
