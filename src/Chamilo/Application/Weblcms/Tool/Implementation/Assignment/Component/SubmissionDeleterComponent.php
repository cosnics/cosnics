<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.weblcms.tool.assignment.php.component Deleter for submissions.
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Bert De Clercq (Hogeschool Gent)
 */
class SubmissionDeleterComponent extends Manager
{

    public function run()
    {
        $pub = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(),
            Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID));

        if (! $this->is_allowed(WeblcmsRights :: DELETE_RIGHT, $pub))
        {
            throw new NotAllowedException();
        }

        $submission_ids = $this->getRequest()->get(self :: PARAM_SUBMISSION);

        if (! is_array($submission_ids))
        {
            $submission_ids = array($submission_ids);
        }

        $submission_tracker = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission();

        foreach ($submission_ids as $sid)
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: class_name(),
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: PROPERTY_ID),
                new StaticConditionVariable($sid));

            $submission = DataManager :: retrieve(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: class_name(),
                new DataClassRetrieveParameters($condition));

            $submission_id = $submission->get_id();
            $publication_id = $submission->get_publication_id();
            $submitter_type = $submission->get_submitter_type();
            $submitter_id = $submission->get_submitter_id();
            $submission->delete();

            // delete the feedbacks of the submission
            $feedback_tracker = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback();
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback :: class_name(),
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback :: PROPERTY_SUBMISSION_ID),
                new StaticConditionVariable($submission_id));
            $feedback_tracker->remove($condition);

            // delete the score of the submission
            $score_tracker = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore();

            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: class_name(),
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: PROPERTY_SUBMISSION_ID),
                new StaticConditionVariable($submission_id));

            $score_tracker->remove($condition);

            // delete the note of the submission
            $note_tracker = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionNote();

            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionNote :: class_name(),
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionNote :: PROPERTY_SUBMISSION_ID),
                new StaticConditionVariable($submission_id));

            $note_tracker->remove($condition);
        }

        $params = array(
            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_BROWSE_SUBMISSIONS,
            self :: PARAM_PUBLICATION_ID => $publication_id,
            self :: PARAM_SUBMITTER_TYPE => $submitter_type,
            self :: PARAM_TARGET_ID => $submitter_id);

        $this->redirect(Translation :: get('SubmissionDeleted'), false, $params);
    }
}
