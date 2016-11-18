<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block with an overview of all course groups the
 *          assignment is published for and their submission stats
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Anthony Hurst (Hogeschool Gent)
 */
class AssignmentCourseGroupsBlock extends AssignmentSubmittersBlock
{

    /**
     * #Override (Implementation) Obtains the course groups registered to see the assignment, sorted alphabetically by
     * name.
     * 
     * @return array() The course groups registered to see the assignment, sorted alphabetically by name.
     */
    public function get_submitters()
    {
        $order_by = array();
        $order_by[] = new OrderBy(
            new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_NAME));
        return \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_publication_target_course_groups(
            $this->get_publication_id(), 
            $this->get_course_id(), 
            null, 
            null, 
            $order_by)->as_array();
    }

    /**
     * #Override (Implementation) Gets the users, course groups and platform groups the assignment was published for
     * plus the number of their submissions and feedbacks
     */
    public function get_submitters_data()
    {
        // Course groups
        $submissions_resultset = \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataManager::retrieve_submissions_by_submitter_type(
            $this->get_publication_id(), 
            AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP);
        
        $this->get_array_from_resultset($submissions_resultset, $this->submissions);
        
        $feedbacks_resultset = \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataManager::retrieve_submitter_feedbacks(
            $this->get_publication_id(), 
            AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP);
        $this->get_array_from_resultset($feedbacks_resultset, $this->feedbacks);
    }

    /**
     * Obtains trhe name of the submitter.
     * 
     * @param $submitter type The submitter whose name is to be obtained.
     * @return string The name of the submitter.
     */
    public function get_submitter_name($submitter)
    {
        return $submitter->get_name();
    }
}
