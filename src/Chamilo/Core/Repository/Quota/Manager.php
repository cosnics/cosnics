<?php
namespace Chamilo\Core\Repository\Quota;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'quota_action';
    const PARAM_REQUEST_ID = 'request_id';
    const PARAM_RESET_CACHE = 'reset_cache';
    const ACTION_BROWSE = 'Browser';
    const ACTION_UPGRADE = 'Upgrader';
    const ACTION_CREATE = 'Creator';
    const ACTION_DELETE = 'Deleter';
    const ACTION_DENY = 'Denier';
    const ACTION_GRANT = 'Granter';
    const ACTION_RIGHTS = 'Rights';
    const DEFAULT_ACTION = self::ACTION_BROWSE;
}
