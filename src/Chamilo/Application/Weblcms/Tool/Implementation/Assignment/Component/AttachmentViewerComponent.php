<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager as WeblcmsTrackingDataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This component allows a user to view an attachment.
 *
 * @author Bert De Clercq (Hogeschool Gent)
 */
class AttachmentViewerComponent extends SubmissionsManager
{
    const TYPE_SUBMISSION = 'submission';
    const TYPE_AUTOMATIC_FEEDBACK = 'automatic_feedback';

    /**
     * Checks whether an object is really attached to a context determined by the attachment type parameter form the
     * url.
     *
     * @return boolean True if the object is attached in the correct context for the item
     */
    public function is_object_attached_in_context()
    {
        $type = Request :: get(self :: PARAM_ATTACHMENT_TYPE);

        switch ($type)
        {
            case self :: TYPE_SUBMISSION :
                return $this->is_submission_attachment();
            case self :: TYPE_AUTOMATIC_FEEDBACK :
                return $this->is_automatic_feedback_attachment();
            default :
                if ($this->is_submission_feedback_attachment())
                {
                    return true;
                }
                return $this->is_publication_attachment();
        }
    }

    /**
     * Checks whether the object you want to view is really attached to the submission. It will get the submission id
     * and the object id from the url.
     *
     * @return boolean True if the object is attached to the submission.
     */
    private function is_submission_attachment()
    {
        $assignment_submission = WeblcmsTrackingDataManager :: retrieve_by_id(
            AssignmentSubmission :: class_name(),
            $this->get_submission_id());

        if ($assignment_submission->get_publication_id() == $this->get_publication_id() &&
             $assignment_submission->get_content_object_id() == $this->get_object_id())
        {
            return true;
        }

        return false;
    }

    /**
     * Checks whether or not the object you want to view is attached to the feedback of a submission
     *
     * @return bool
     */
    protected function is_submission_feedback_attachment()
    {
        if (is_null($this->get_submission_id()))
        {
            return false;
        }

        $assignment_submission = WeblcmsTrackingDataManager :: retrieve_by_id(
            AssignmentSubmission :: class_name(),
            $this->get_submission_id());

        if ($assignment_submission->get_publication_id() != $this->get_publication_id())
        {
            return false;
        }

        $feedbacks = WeblcmsTrackingDataManager :: retrieves(
            SubmissionFeedback :: class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(
                    SubmissionFeedback :: class_name(),
                    SubmissionFeedback :: PROPERTY_SUBMISSION_ID),
                new StaticConditionVariable($assignment_submission->get_id())));

        while ($feedback = $feedbacks->next_result())
        {
            $content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $feedback->get_content_object_id());
            if ($content_object->is_attached_to_or_included_in($this->get_object_id()))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks whether the automatic feedback object you want to view is really attached to the assignment. It will get
     * the assignment via the publication id from the url and the object id from the url.
     *
     * @return boolean True if the automatic feedback object is attached to the assignment
     */
    private function is_automatic_feedback_attachment()
    {
        $assignment = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(),
            $this->get_publication_id())->get_content_object();

        $automatic_feedback_co_ids_string = $assignment->get_automatic_feedback_co_ids();
        $automatic_feedback_co_ids_array = explode(",", $automatic_feedback_co_ids_string);
        $key = array_search($this->get_object_id(), $automatic_feedback_co_ids_array);

        if (! ($key === false))
        {
            return true;
        }
        else
        {
            foreach ($automatic_feedback_co_ids_array as $automatic_feedback_co_id)
            {
                $content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                    ContentObject :: class_name(),
                    $automatic_feedback_co_id);

                if ($content_object->is_attached_to_or_included_in($this->get_object_id()))
                {
                    return true;
                }
            }

            return false;
        }
    }

    /**
     * Checks whether the object you want to view is really attached to the publication. It will get the publication id
     * and object id from the url.
     *
     * @return boolean True if the object is attached to the publication
     */
    private function is_publication_attachment()
    {
        $publication = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(),
            $this->get_publication_id());

        return $publication->get_content_object()->is_attached_to_or_included_in($this->get_object_id());
    }

    /**
     * Adds additional parameters for automatic registration (usefull for url building etc)
     *
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_ATTACHMENT_TYPE, self :: PARAM_SUBMISSION);
    }
}
