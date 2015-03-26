<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Feedback;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 * Portfolio feedback manager
 *
 * @package repository\content_object\portfolio\feedback
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_ACTION = 'feedback_action';
    const PARAM_FEEDBACK_ID = 'feedback_id';

    // Actions
    const ACTION_BROWSE = 'Browser';
    const ACTION_DELETE = 'Deleter';
    const ACTION_UPDATE = 'Updater';
    const ACTION_SUBSCRIBER = 'Subscriber';
    const ACTION_UNSUBSCRIBER = 'Unsubscriber';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    /**
     * Determine if the feedback can be updated
     *
     * @param \core\repository\content_object\portfolio\feedback\Feedback $feedback
     * @return boolean
     */
    public function can_update_feedback($feedback)
    {
        if (method_exists($this->get_parent(), 'can_update_feedback'))
        {
            return $this->get_parent()->can_update_feedback($feedback);
        }

        return true;
    }

    /**
     * Determine if the feedback can be deleted
     *
     * @param \core\repository\content_object\portfolio\feedback\Feedback $feedback
     * @return boolean
     */
    public function can_delete_feedback($feedback)
    {
        if (method_exists($this->get_parent(), 'can_delete_feedback'))
        {
            return $this->get_parent()->can_delete_feedback($feedback);
        }

        return true;
    }
}
