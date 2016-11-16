<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.weblcms.tool.assignment.php.component Updater for submission feedback.
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Bert De Clercq (Hogeschool Gent)
 */
class FeedbackUpdaterComponent extends Manager
{

    public function run()
    {
        $feedback_id = Request::get(self::PARAM_FEEDBACK_ID);
        $tracker = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::PROPERTY_ID), 
            new StaticConditionVariable($feedback_id));
        
        $feedback = DataManager::retrieve(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::class_name(), 
            new DataClassRetrieveParameters($condition));
        
        $feedback_content_object = $feedback->get_content_object();
        
        $object_form = ContentObjectForm::factory(
            ContentObjectForm::TYPE_EDIT, 
            new PersonalWorkspace($this->get_user()), 
            $feedback_content_object, 
            'edit', 
            'post', 
            $this->get_url(array(self::PARAM_FEEDBACK_ID => $feedback_id)), 
            null, 
            null, 
            false);
        
        $publication_id = Request::get(self::PARAM_PUBLICATION_ID);
        $target_id = Request::get(self::PARAM_TARGET_ID);
        $submitter_type = Request::get(self::PARAM_SUBMITTER_TYPE);
        
        if ($object_form->validate())
        {
            $success = $object_form->update_content_object();
            $this->redirect(
                Translation::get($success ? 'FeedbackUpdated' : 'UpdateFailed'), 
                ! $success, 
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW_SUBMISSION, 
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id, 
                    self::PARAM_TARGET_ID => $target_id, 
                    self::PARAM_SUBMITTER_TYPE => $submitter_type, 
                    self::PARAM_SUBMISSION => $feedback->get_submission_id()));
        }
        else
        {
            $html = array();
            
            $html[] = $this->render_header();
            $html[] = $object_form->toHtml();
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $submission_id = Request::get(self::PARAM_SUBMISSION);
        $tracker = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_ID), 
            new StaticConditionVariable($submission_id));
        
        $submission_tracker = DataManager::retrieve(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
            new DataClassRetrieveParameters($condition));
        
        $submissions = \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::get_data(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
            null, 
            $condition)->as_array();
        $submission = $submissions[0]->get_content_object();
        
        $pub = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $submission_tracker->get_publication_id());
        
        if (! $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $pub))
        {
            throw new NotAllowedException();
        }
        $assignment = $pub->get_content_object();
        
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_BROWSE)), 
                Translation::get('BrowserComponent')));
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_BROWSE_SUBMITTERS, 
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $submission_tracker->get_publication_id())), 
                $assignment->get_title()));
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_BROWSE_SUBMISSIONS, 
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $submission_tracker->get_publication_id(), 
                        self::PARAM_TARGET_ID => $submission_tracker->get_submitter_id(), 
                        self::PARAM_SUBMITTER_TYPE => $submission_tracker->get_submitter_type())), 
                $this->get_submitter_name($submission_tracker)));
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW_SUBMISSION, 
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $submission_tracker->get_publication_id(), 
                        self::PARAM_TARGET_ID => $submission_tracker->get_submitter_id(), 
                        self::PARAM_SUBMITTER_TYPE => $submission_tracker->get_submitter_type(), 
                        self::PARAM_SUBMISSION => $submission_id)), 
                $submission->get_title() . ' - ' . Translation::get('Detail')));
    }

    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, 
            self::PARAM_FEEDBACK_ID, 
            self::PARAM_SUBMISSION);
    }

    /**
     * Returns the name of the submitter as a string.
     * When submitted as a group, it will return the name of the user who
     * submitted followed by the group name.
     * 
     * @return string The name of the submitter
     */
    private function get_submitter_name($submission_tracker)
    {
        // name of the user who submitted
        $user_name = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User::class_name(), 
            $submission_tracker->get_user_id());
        
        switch ($submission_tracker->get_submitter_type())
        {
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER :
                return $user_name->get_fullname();
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP :
                return $user_name->get_fullname() . ' - ' . \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
                    CourseGroup::class_name(), 
                    $submission_tracker->get_submitter_id())->get_name();
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP :
                return $user_name->get_fullname() . ' - ' . \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(
                    Group::class_name(), 
                    $submission_tracker->get_submitter_id())->get_name();
        }
    }
}
