<?php
namespace Chamilo\Application\CasUser\Account;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'account_action';
    const PARAM_ACCOUNT_ID = 'account_id';
    const ACTION_ACTIVATE = 'activater';
    const ACTION_BROWSE = 'browser';
    const ACTION_CREATE = 'creator';
    const ACTION_DEACTIVATE = 'deactivater';
    const ACTION_DELETE = 'deleter';
    const ACTION_UPDATE = 'updater';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
