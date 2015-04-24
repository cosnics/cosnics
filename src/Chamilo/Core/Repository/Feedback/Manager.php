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

    public function can_update_feedback($feedback)
    {
        if (method_exists($this->get_parent(), 'can_update_feedback'))
        {
            return $this->get_parent()->can_update_feedback($feedback);
        }

        return true;
    }

    public function can_delete_feedback($feedback)
    {
        if (method_exists($this->get_parent(), 'can_delete_feedback'))
        {
            return $this->get_parent()->can_delete_feedback($feedback);
        }

        return true;
    }
}
