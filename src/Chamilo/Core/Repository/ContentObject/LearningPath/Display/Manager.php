<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
{
    // Actions
    const ACTION_FEEDBACK = 'Feedback';
    const ACTION_BOOKMARK = 'Bookmarker';
    const ACTION_ACTIVITY = 'Activity';
    const ACTION_RIGHTS = 'Rights';
    const ACTION_MOVE = 'Mover';
    const ACTION_SORT = 'Sorter';
    const ACTION_MANAGE = 'Manager';
    const ACTION_USER = 'User';
    const ACTION_BUILD_PREREQUISITES = 'PrerequisitesBuilder';
    const ACTION_TYPE_SPECIFIC = 'TypeSpecific';
    const ACTION_BUILD = 'Builder';
    const ACTION_REPORTING = 'Reporting';
    const ACTION_ATTEMPT = 'Attempt';

    // Parameters
    const PARAM_STEP = 'step';
    const PARAM_SHOW_PROGRESS = 'show_progress';
    const PARAM_DETAILS = 'details';
    const PARAM_LEARNING_PATH_ITEM_ID = 'learning_path_item_id';
    const PARAM_SORT = 'sort';
    const PARAM_ITEM_ATTEMPT_ID = 'item_attempt_id';

    // Sorting
    const SORT_UP = 'Up';
    const SORT_DOWN = 'Down';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

    /**
     * Get the id of the currently requested step
     *
     * @return int
     */
    public function get_current_step()
    {
        if (! isset($this->current_step))
        {
            if ($this->is_current_step_set())
            {
                $this->current_step = $this->get_current_step_from_request();

                if (is_array($this->current_step))
                {
                    $this->current_step = $this->current_step[0];
                }
            }
            else
            {
                $this->current_step = $this->get_complex_content_object_path()->get_root()->get_id();
            }
        }

        return $this->current_step;
    }

    /**
     *
     * @return boolean
     */
    public function is_current_step_set()
    {
        return ! is_null(Request :: get(self :: PARAM_STEP));
    }

    /**
     *
     * @return int
     */
    private function get_current_step_from_request()
    {
        return Request :: get(self :: PARAM_STEP);
    }

    /**
     * Get the content object linked to the current step
     *
     * @return \core\repository\ContentObject
     */
    public function get_current_content_object()
    {
        return $this->get_current_node()->get_content_object();
    }

    /**
     * Get the complex content object item linked to the current step
     *
     * @return \core\repository\storage\data_class\ComplexContentObjectItem
     */
    public function get_current_complex_content_object_item()
    {
        return $this->get_current_node()->get_complex_content_object_item();
    }

    /**
     * Get the node linked to the current step
     *
     * @return \core\repository\common\path\ComplexContentObjectPathNode
     */
    public function get_current_node()
    {
        return $this->get_complex_content_object_path()->get_node($this->get_current_step());
    }

    public function get_complex_content_object_path()
    {
        $learning_path_item_attempt_data = $this->get_parent()->retrieve_learning_path_tracker_items(
            $this->get_parent()->retrieve_learning_path_tracker());

        return $this->get_parent()->get_root_content_object()->get_complex_content_object_path(
            $learning_path_item_attempt_data);
    }

    /**
     *
     * @see \libraries\architecture\application\Application::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_STEP, $this->get_current_step());
    }
   
}