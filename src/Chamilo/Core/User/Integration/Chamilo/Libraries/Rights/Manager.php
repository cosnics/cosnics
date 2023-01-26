<?php
namespace Chamilo\Core\User\Integration\Chamilo\Libraries\Rights;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 * @package Chamilo\Core\User\Integration\Chamilo\Libraries\Rights
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends AjaxManager
{
    public const ACTION_USER_ENTITY_FEED = 'UserEntityFeed';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_USER_ENTITY_FEED;
}
