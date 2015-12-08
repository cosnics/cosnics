<?php
namespace Chamilo\Core\Repository\Feedback;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'feedback_action';
    const PARAM_FEEDBACK_ID = 'feedback_id';
    const ACTION_BROWSE = 'Browser';
    const ACTION_DELETE = 'Deleter';
    const ACTION_UPDATE = 'Updater';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
