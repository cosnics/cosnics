<?php
namespace Chamilo\Core\User\Integration\Chamilo\Libraries\Rights;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 * @package Chamilo\Core\User\Integration\Chamilo\Libraries\Rights
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends AjaxManager
{
    const ACTION_USER_ENTITY_FEED = 'UserEntityFeed';

    const DEFAULT_ACTION = self::ACTION_USER_ENTITY_FEED;
}
