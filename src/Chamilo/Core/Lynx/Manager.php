<?php
namespace Chamilo\Core\Lynx;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const ACTION_BROWSE = 'Browser';
    const ACTION_SOURCE = 'Source';
    const ACTION_REMOTE = 'Remote';
    const ACTION_UPGRADE = 'Upgrader';
    const ACTION_CONTENT_OBJECT_UPGRADE = 'ContentObjectUpgrader';
    const ACTION_APPLICATION_UPGRADE = 'ApplicationUpgrader';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}