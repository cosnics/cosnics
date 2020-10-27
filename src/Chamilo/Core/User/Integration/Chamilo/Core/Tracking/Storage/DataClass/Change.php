<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataClass\ChangesTracker;

/**
 * This class tracks the login that a user uses
 */
class Change extends ChangesTracker
{
    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'tracking_user_change';
    }
}
