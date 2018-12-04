<?php
namespace Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 * @package Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends AjaxManager
{
    const ACTION_GROUP_ENTITY_FEED = 'UserEntityFeed';

    const DEFAULT_ACTION = self::ACTION_GROUP_ENTITY_FEED;
}
