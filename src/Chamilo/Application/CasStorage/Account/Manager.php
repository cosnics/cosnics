<?php
namespace Chamilo\Application\CasStorage\Account;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    // Parameters
    const PARAM_ACTION = 'account_action';
    const PARAM_ACCOUNT_ID = 'account_id';
    
    // Actions
    const ACTION_ACTIVATE = 'Activater';
    const ACTION_BROWSE = 'Browser';
    const ACTION_CREATE = 'Creator';
    const ACTION_DEACTIVATE = 'Deactivater';
    const ACTION_DELETE = 'Deleter';
    const ACTION_UPDATE = 'Updater';
    
    // Default action
    const DEFAULT_ACTION = self::ACTION_BROWSE;
}
