<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Tracking;

use Chamilo\Core\Tracking\Storage\DataClass\AggregateTracker;

/**
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Tracking
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class User extends AggregateTracker
{
    public const CONTEXT = 'Chamilo\Core\User\Integration\Chamilo\Core\Tracking';

    public const TYPE_BROWSER = 'browser';
    public const TYPE_COUNTRY = 'country';
    public const TYPE_OPERATING_SYSTEM = 'operating_system';
    public const TYPE_PROVIDER = 'provider';
    public const TYPE_REFERER = 'referer';

    public static function getStorageUnitName(): string
    {
        return 'tracking_user_user';
    }
}
