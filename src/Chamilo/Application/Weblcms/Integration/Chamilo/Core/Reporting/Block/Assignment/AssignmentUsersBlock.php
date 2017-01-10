<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataManager as AssignmentDataManager;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block with an overiew of all users the assigment is
 *          published for and their submission stats
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Anthony Hurst (Hogeschool Gent)
 */
class AssignmentUsersBlock extends AssignmentSubmittersBlock
{

    public function get_submitters()
    {
        $order_by = array();
        return \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_publication_target_users(
            $this->get_publication_id(), 
            $this->get_course_id(), 
            null, 
            null, 
            $order_by)->as_array();
    }

    /**
     * Gets the users, course groups and platform groups the assignment was published for plus the number of their
     * submissions and feedbacks
     */
    public function get_submitters_data()
    {
        // Users
        $submissions_resultset = AssignmentDataManager::retrieve_submissions_by_submitter_type(
            $this->get_publication_id(), 
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER, 
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name());
        $this->get_array_from_resultset($submissions_resultset, $this->submissions);
        $feedbacks_resultset = AssignmentDataManager::retrieve_submitter_feedbacks(
            $this->get_publication_id(), 
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER, 
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name());
        $this->get_array_from_resultset($feedbacks_resultset, $this->feedbacks);
    }

    /**
     * Obtains the name of the submitter.
     * 
     * @param $submitter type The submitter whose name is to be obtained.
     * @return string The name of the submitter.
     */
    public function get_submitter_name($submitter)
    {
        if($submitter instanceof User)
        {
            return $submitter->get_fullname();
        }
    }
}
