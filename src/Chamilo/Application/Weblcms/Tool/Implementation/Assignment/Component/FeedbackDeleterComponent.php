<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.weblcms.tool.assignment.php.component Deleter for submission feedback.
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class FeedbackDeleterComponent extends Manager
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

        $ids = Request :: get(self :: PARAM_FEEDBACK_ID);
        if (! is_array($ids))
        {
            $feedback_ids[] = $ids;
        }
        else
        {
            $feedback_ids = $ids;
        }
        $tracker = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback();

        foreach ($feedback_ids as $fid)
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback :: class_name(),
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback :: PROPERTY_ID),
                new StaticConditionVariable($fid));

            $feedbacks = DataManager :: retrieves(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback :: class_name(),
                new DataClassRetrievesParameters($condition))->as_array();

            foreach ($feedbacks as $feedback)
            {
                $submission_id = $feedback->get_submission_id();
                $feedback->delete();
            }
        }

        $publication_id = Request :: get(self :: PARAM_PUBLICATION_ID);
        $target_id = Request :: get(self :: PARAM_TARGET_ID);
        $submitter_type = Request :: get(self :: PARAM_SUBMITTER_TYPE);

        $params = array(
            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_VIEW_SUBMISSION,
            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication_id,
            self :: PARAM_TARGET_ID => $target_id,
            self :: PARAM_SUBMITTER_TYPE => $submitter_type,
            self :: PARAM_SUBMISSION => $submission_id);

        $this->redirect(Translation :: get('FeedbackDeleted'), false, $params);
    }
}
