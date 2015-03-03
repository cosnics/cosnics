<?php
namespace Chamilo\Core\Lynx\Remote;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'manager_action';
    const PARAM_PACKAGE_ID = 'package_id';
    const ACTION_BROWSE = 'browser';
    const ACTION_SYNCHRONIZE = 'synchronizer';
    const ACTION_DOWNLOAD = 'download';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

}
