<?php
namespace Chamilo\Core\Repository\Quota;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'quota_action';
    const PARAM_REQUEST_ID = 'request_id';
    const PARAM_RESET_CACHE = 'reset_cache';
    const ACTION_BROWSE = 'browser';
    const ACTION_UPGRADE = 'upgrader';
    const ACTION_CREATE = 'creator';
    const ACTION_DELETE = 'deleter';
    const ACTION_DENY = 'denier';
    const ACTION_GRANT = 'granter';
    const ACTION_RIGHTS = 'rights';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
