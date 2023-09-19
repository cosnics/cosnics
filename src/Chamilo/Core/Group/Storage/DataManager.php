<?php
namespace Chamilo\Core\Group\Storage;

/**
 * @package group.lib
 */

/**
 * This is a skeleton for a data manager for the Users table.
 * Data managers must extend this class and implement its
 * abstract methods.
 *
 * @author     Hans De Bisschop
 * @author     Dieter De Neef
 * @deprecated Use the GroupService and associated services now
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    public const PREFIX = 'group_';

    public static mixed $allSubscribedGroupsCache = [];
}
