<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Tracking;

use Chamilo\Core\Tracking\Storage\DataClass\AggregateTracker;

/**
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Tracking
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class User extends AggregateTracker
{
    const TYPE_BROWSER = 'browser';
    const TYPE_COUNTRY = 'country';
    const TYPE_OPERATING_SYSTEM = 'operating_system';
    const TYPE_PROVIDER = 'provider';
    const TYPE_REFERER = 'referer';

    public static function getStorageUnitName(): string
    {
        return 'tracking_user_user';
    }
}
