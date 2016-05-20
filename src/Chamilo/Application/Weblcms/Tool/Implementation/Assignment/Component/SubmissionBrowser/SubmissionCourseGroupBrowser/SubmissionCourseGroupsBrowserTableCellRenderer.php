<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionCourseGroupBrowser;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionGroupsBrowser\SubmissionGroupsBrowserTableCellRenderer;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager as CourseGroupDataManager;

/**
 *
 * @package application.weblcms.tool.assignment.php.component.submission_browser This class is a cell renderer for a
 *          group submissions browser table
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Anthony Hurst (Hogeschool Gent)
 * @author Bert De Clercq (Hogeschool Gent)
 */
class SubmissionCourseGroupsBrowserTableCellRenderer extends SubmissionGroupsBrowserTableCellRenderer
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the submitter type for this table cell renderer
     * 
     * @return int
     */
    public function get_submitter_type()
    {
        return AssignmentSubmission :: SUBMITTER_TYPE_COURSE_GROUP;
    }

    /**
     * Returns whether or not a user is subscribed in a group
     * 
     * @param int $group_id
     * @param int $user_id
     *
     * @return bool
     */
    protected function is_subscribed_in_group($group_id, $user_id)
    {
        return CourseGroupDataManager :: is_course_group_member($group_id, $user_id);
    }

    /**
     * Retrieves the user_ids of a group
     * 
     * @param $group_id
     * @return int[]
     */
    protected function retrieve_group_user_ids($group_id)
    {
        return CourseGroupDataManager :: retrieve_course_group_user_ids($group_id);
    }

    /**
     * Recursively iterates over a course group its subgroups to identify whether the current user is a member of the
     * course group at any level.
     * 
     * @param $group the course group.
     * @param int $user_id
     *
     * @return boolean
     */
    protected function is_subgroup_member($group, $user_id)
    {
        foreach ($group->get_children() as $subgroup)
        {
            if ($this->is_group_member($subgroup, $user_id))
            {
                return true;
            }
        }
        return false;
    }
}
