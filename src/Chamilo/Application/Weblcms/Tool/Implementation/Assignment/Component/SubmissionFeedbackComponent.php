<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.weblcms.tool.assignment.php.component Component for the submission feedback form for assignment
 *          publications. Uses repoviewer
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Bert De Clercq (Hogeschool Gent)
 */
class SubmissionFeedbackComponent extends Manager implements \Chamilo\Core\Repository\Viewer\ViewerInterface
{

    /**
     * ID of the submission
     * 
     * @var int
     */
    private $submission_id;

    /**
     * The assignment content object
     * 
     * @var Assignment
     */
    private $assignment;

    public function run()
    {
        $publication_id = Request::get(self::PARAM_PUBLICATION_ID);
        $target_id = Request::get(self::PARAM_TARGET_ID);
        $submitter_type = Request::get(self::PARAM_SUBMITTER_TYPE);
        
        // display form when content object created in RepoViewer
        if (\Chamilo\Core\Repository\Viewer\Manager::is_ready_to_be_published())
        {
            $this->create_tracker();
            $this->redirect(
                Translation::get('FeedbackCreated'), 
                false, 
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW_SUBMISSION, 
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id, 
                    self::PARAM_TARGET_ID => $target_id, 
                    self::PARAM_SUBMITTER_TYPE => $submitter_type, 
                    self::PARAM_SUBMISSION => $this->submission_id));
        }
        
        // construct RepoViewer when no content object created yet
        if (! \Chamilo\Core\Repository\Viewer\Manager::is_ready_to_be_published())
        {
            $factory = new ApplicationFactory(
                \Chamilo\Core\Repository\Viewer\Manager::context(), 
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            $component = $factory->getComponent();
            $component->set_maximum_select(\Chamilo\Core\Repository\Viewer\Manager::SELECT_SINGLE);
            $component->set_parameter(self::PARAM_SUBMISSION, $this->submission_id);
            return $component->run();
        }
    }

    /**
     * Creates a submission feedback tracker
     * 
     * @param $values array exported values of the form
     */
    public function create_tracker()
    {
        // TODO: Check for correctness
        $arguments = array(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::PROPERTY_SUBMISSION_ID => $this->submission_id, 
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::PROPERTY_CREATED => time(), 
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::PROPERTY_MODIFIED => time(), 
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::PROPERTY_USER_ID => $this->get_user_id(), 
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::PROPERTY_CONTENT_OBJECT_ID => $this->get_repo_object());
        Event::trigger('FeedbackSubmission', \Chamilo\Application\Weblcms\Manager::context(), $arguments);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $submissionTranslation = Translation::getInstance()->getTranslation('Submission');

        $publication_id = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        $this->submission_id = Request::get(self::PARAM_SUBMISSION);

        if(empty($this->submission_id))
        {
            throw new NoObjectSelectedException($submissionTranslation);
        }

        $tracker = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_ID), 
            new StaticConditionVariable($this->submission_id));
        
        $submission_tracker = DataManager::retrieve(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
            new DataClassRetrieveParameters($condition));
        
        $submissions = \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::get_data(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
            null, 
            $condition)->as_array();

        if(empty($submissions))
        {
            throw new ObjectNotExistException($submissionTranslation, $this->submission_id);
        }

        $submission = $submissions[0]->get_content_object();


        
        $pub = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $publication_id);
        
        if (! $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $pub))
        {
            $this->redirect(
                Translation::get("NotAllowed", null, Utilities::COMMON_LIBRARIES), 
                true, 
                array(), 
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION, 
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, 
                    self::PARAM_TARGET_ID, 
                    self::PARAM_SUBMITTER_TYPE, 
                    self::PARAM_SUBMISSION));
        }
        $this->assignment = $pub->get_content_object();
        
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_BROWSE_SUBMITTERS, 
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication_id)), 
                $this->assignment->get_title()));
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
                        self::PARAM_SUBMISSION => $this->submission_id)), 
                $submission->get_title() . ' - ' . Translation::get('Detail')));
    }

    public function get_allowed_content_object_types()
    {
        return explode(',', $this->assignment->get_allowed_types());
    }

    /**
     * Gets the selected object id of the RepoViewer
     * 
     * @return int
     */
    public function get_repo_object()
    {
        return \Chamilo\Core\Repository\Viewer\Manager::get_selected_objects();
    }

    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, 
            self::PARAM_TARGET_ID, 
            self::PARAM_SUBMITTER_TYPE, 
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
