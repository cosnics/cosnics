<?php
namespace Chamilo\Core\Admin\Announcement;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'announcement_action';
    const PARAM_SYSTEM_ANNOUNCEMENT_ID = 'announcement';
    const ACTION_CREATE = 'Creator';
    const ACTION_BROWSE = 'Browser';
    const ACTION_EDIT = 'Editor';
    const ACTION_DELETE = 'Deleter';
    const ACTION_VIEW = 'Viewer';
    const ACTION_HIDE = 'Hider';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
