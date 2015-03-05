<?php
namespace Chamilo\Core\Admin\Announcement;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'announcement_action';
    const PARAM_SYSTEM_ANNOUNCEMENT_ID = 'announcement';
    const ACTION_CREATE = 'creator';
    const ACTION_BROWSE = 'browser';
    const ACTION_EDIT = 'editor';
    const ACTION_DELETE = 'deleter';
    const ACTION_VIEW = 'viewer';
    const ACTION_HIDE = 'hider';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
