<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Tracking;

use Chamilo\Core\Tracking\Storage\DataClass\AggregateTracker;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataManager;

abstract class User extends AggregateTracker
{
    const TYPE_BROWSER = 'browser';
    const TYPE_COUNTRY = 'country';
    const TYPE_OPERATING_SYSTEM = 'operating_system';
    const TYPE_PROVIDER = 'provider';
    const TYPE_REFERER = 'referer';

    public static function get_table_name()
    {
        return DataManager :: PREFIX . 'user';
    }
}
