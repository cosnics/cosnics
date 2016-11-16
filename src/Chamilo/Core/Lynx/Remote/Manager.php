<?php
namespace Chamilo\Core\Lynx\Remote;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'manager_action';
    const PARAM_PACKAGE_ID = 'package_id';
    const ACTION_BROWSE = 'Browser';
    const ACTION_SYNCHRONIZE = 'Synchronizer';
    const ACTION_DOWNLOAD = 'Download';
    const DEFAULT_ACTION = self::ACTION_BROWSE;
}
