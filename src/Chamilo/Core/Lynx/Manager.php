<?php
namespace Chamilo\Core\Lynx;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const APPLICATION_NAME = 'lynx';
    const ACTION_BROWSE = 'browser';
    const ACTION_SOURCE = 'source';
    const ACTION_REMOTE = 'remote';
    const ACTION_UPGRADE = 'upgrader';
    const ACTION_CONTENT_OBJECT_UPGRADE = 'content_object_upgrader';
    const ACTION_APPLICATION_UPGRADE = 'application_upgrader';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
