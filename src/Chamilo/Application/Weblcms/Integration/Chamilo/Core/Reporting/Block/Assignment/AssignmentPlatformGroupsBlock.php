<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataManager as AssignmentDataManager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block with an overview of all platform groups the
 *          assignment is published for and their submission stats
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Anthony Hurst (Hogeschool Gent)
 */
class AssignmentPlatformGroupsBlock extends AssignmentSubmittersBlock
{

    /**
     * #Override (Implementation) Obtains the groups registered with the assignment, sorted alphabetically by name.
     *
     * @return array() The list of platform groups registered to see the assignment, sorted alphabetically by name.
     */
    public function get_submitters()
    {
        $order_by = array();
        $order_by[] = new OrderBy(new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_NAME));
        return \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_publication_target_platform_groups(
            $this->get_publication_id(),
            $this->get_course_id(),
            null,
            null,
            $order_by)->as_array();
    }

    /**
     * #Override (Implementation) Gets the platform groups the assignment was published for plus the number of their
     * submissions and feedbacks
     */
    public function get_submitters_data()
    {
        $submissions_resultset = AssignmentDataManager :: retrieve_submissions_by_submitter_type(
            $this->get_publication_id(),
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_PLATFORM_GROUP,
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: class_name());

        $this->get_array_from_resultset($submissions_resultset, $this->submissions);

        $feedbacks_resultset = AssignmentDataManager :: retrieve_submitter_feedbacks(
            $this->get_publication_id(),
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_PLATFORM_GROUP,
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: class_name());

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
        return $submitter->get_name();
    }
}
