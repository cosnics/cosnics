<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission;
use Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionSubmit\SubmissionSubmitWizardComponent;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Forms\SubmissionSubmitForm;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager as CourseGroupDataManager;
use Chamilo\Configuration\Configuration;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\Common\Action\ContentObjectCopier;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Cache\DataClassCache;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.weblcms.tool.assignment.php.component Component for the submission feedback form for assignment
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Bert De Clercq (Hogeschool Gent)
 * @author Anthony Hurst (Hogeschool Gent)
 */
class SubmissionSubmitComponent extends SubmissionSubmitWizardComponent implements 
    \Chamilo\Core\Repository\Viewer\ViewerInterface
{

    /**
     * The id of the assignment publication
     * 
     * @var int
     */
    private $publication_id;

    /**
     * The assignment content object
     * 
     * @var Assignment
     */
    private $assignment;

    public function run()
    {
        $choices = $this->compile_choices();
        // display submission form when content object created in RepoViewer
        if ($this->assignment->get_allow_group_submissions() && count($choices) == 0 ||
             ! $this->is_allowed(WeblcmsRights::VIEW_RIGHT))
        {
            if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
            {
                $this->redirect(
                    Translation::get('NoOwnGroups'), 
                    true, 
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_BROWSE_SUBMITTERS, 
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->publication_id));
            }
            $this->redirect(
                Translation::get("NoOwnGroups", null, Utilities::COMMON_LIBRARIES), 
                true, 
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_STUDENT_BROWSE_SUBMISSIONS, 
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->publication_id));
        }
        
        if ($this->allowGroupSubmissions() && ! $this->submissionTargetSelected())
        {
            $form = new SubmissionSubmitForm(
                $choices, 
                $this->get_url(
                    array(
                        \Chamilo\Core\Repository\Viewer\Manager::PARAM_CONTENT_OBJECT_TYPE => Request::get(
                            \Chamilo\Core\Repository\Viewer\Manager::PARAM_CONTENT_OBJECT_TYPE), 
                        \Chamilo\Core\Repository\Viewer\Manager::PARAM_ID => $this->get_repo_objects())));
            
            // create submission feedback tracker when form is valid
            if ($form->validate())
            {
                $values = $form->exportValues();
                $submitter_type = substr(
                    $values[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_ID], 
                    0, 
                    1);
                $submitter_id = substr(
                    $values[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_ID], 
                    1);
                
                $this->set_parameter(self::PARAM_SUBMITTER_TYPE, $submitter_type);
                $this->set_parameter(self::PARAM_TARGET_ID, $submitter_id);
                
                return $this->showRepoViewer();
            }
            else // display submission form
            {
                $html = array();
                
                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();
                
                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            return $this->showRepoViewer();
        }
    }

    /**
     * Shows the repoviewer
     * 
     * @return string
     */
    protected function showRepoViewer()
    {
        if (\Chamilo\Core\Repository\Viewer\Manager::is_ready_to_be_published())
        {
            $submitter = $this->create_tracker($this->get_target_id(), $this->get_repo_objects());
            // $this->sendMail();
            
            $this->redirect(
                '', 
                false, 
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_SUBMIT_SUBMISSON_CONFIRMATION, 
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->publication_id, 
                    self::PARAM_SUBMITTER_TYPE => substr($submitter, 0, 1), 
                    self::PARAM_TARGET_ID => substr($submitter, 1)));
        }
        else
        {
            $result = $this->check_start_end_time();
            
            if ($result === true)
            {
                $factory = new ApplicationFactory(
                    \Chamilo\Core\Repository\Viewer\Manager::context(), 
                    new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
                $component = $factory->getComponent();
                $component->set_maximum_select(\Chamilo\Core\Repository\Viewer\Manager::SELECT_SINGLE);
                
                return $component->run();
            }
            else
            {
                return $result;
            }
        }
        
        return null;
    }

    /**
     * Returns an array of the course and platform groups the currently logged in user is a member of.
     * 
     * @return array The course groups and platform groups
     */
    private function compile_choices()
    {
        $choices = array();
        
        $target_entities = WeblcmsRights::getInstance()->get_target_entities(
            WeblcmsRights::VIEW_RIGHT, 
            \Chamilo\Application\Weblcms\Manager::context(), 
            $this->publication_id, 
            WeblcmsRights::TYPE_PUBLICATION, 
            $this->get_course()->get_id(), 
            WeblcmsRights::TREE_TYPE_COURSE);
        
        if ($this->get_target_id())
        {
            switch ($this->get_submitter_type())
            {
                case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP :
                    return array(
                        \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP .
                             $this->get_target_id() => CourseGroupDataManager::retrieve_by_id(
                                CourseGroup::class_name(), 
                                $this->get_target_id())->get_name());
                case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP :
                    return array(
                        \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP .
                             $this->get_target_id() => \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(
                                Group::class_name(), 
                                $this->get_target_id())->get_name());
            }
        }
        
        $groups_resultset = CourseGroupDataManager::retrieve_course_groups_from_user(
            $this->get_user()->get_id(), 
            $this->get_course()->get_id());
        if ($target_entities[0])
        {
            // add all course groups the user is member of
            while ($course_group = $groups_resultset->next_result())
            {
                $choices[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP .
                     $course_group->get_id()] = $course_group->get_name();
            }
            
            // retrieve platform groups subscribed to course
            $cgrConditions = array();
            $cgrConditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class_name(), 
                    CourseEntityRelation::PROPERTY_COURSE_ID), 
                new StaticConditionVariable($this->get_course()->get_id()));
            $cgrConditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class_name(), 
                    CourseEntityRelation::PROPERTY_ENTITY_TYPE), 
                new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_GROUP));
            
            $group_ids = \Chamilo\Application\Weblcms\Course\Storage\DataManager::distinct(
                CourseEntityRelation::class_name(), 
                new DataClassDistinctParameters(
                    new AndCondition($cgrConditions), 
                    CourseEntityRelation::PROPERTY_ENTITY_ID));
            
            // ***********************************************************************************************//
            // DMTODO Problem with caching. Once caching problems fixed, remove the following line of code. //
            // ***********************************************************************************************//
            DataClassCache::truncate(Group::class_name());
            // ***********************************************************************************************//
            // DMTODO End remove code. //
            // ***********************************************************************************************//
            $groups_resultset = \Chamilo\Core\Group\Storage\DataManager::retrieve_groups_and_subgroups($group_ids);
        }
        else
        {
            // add the target course groups the user is member of
            $targets = CourseGroupDataManager::retrieve_course_groups_and_subgroups(
                $target_entities[CourseGroupEntity::ENTITY_TYPE]);
            
            $target_course_groups = array();
            while ($target = $targets->next_result())
            {
                $target_course_groups[$target->get_id()] = $target->get_id();
            }
            while ($course_group = $groups_resultset->next_result())
            {
                if ($target_course_groups[$course_group->get_id()])
                {
                    $choices[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP .
                         $course_group->get_id()] = $course_group->get_name();
                }
            }
            
            // retrieve target platform groups
            $groups_resultset = \Chamilo\Core\Group\Storage\DataManager::retrieve_groups_and_subgroups(
                $target_entities[CoursePlatformGroupEntity::ENTITY_TYPE]);
        }
        
        // add platform groups the user is member of
        while ($group = $groups_resultset->next_result())
        {
            if (\Chamilo\Core\Group\Storage\DataManager::is_group_member($group->get_id(), $this->get_user()->get_id()))
            {
                $choices[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP .
                     $group->get_id()] = $group->get_name();
            }
        }
        
        return $choices;
    }

    /**
     * Checks if the Assignment has started or stopped yet
     */
    private function check_start_end_time()
    {
        if ($this->assignment->get_start_time() > time())
        {
            $html = array();
            
            $html[] = $this->render_header();
            $date = DatetimeUtilities::format_locale_date(
                Translation::get('DateFormatShort', null, Utilities::COMMON_LIBRARIES) . ', ' .
                     Translation::get('TimeNoSecFormat', null, Utilities::COMMON_LIBRARIES), 
                    $this->assignment->get_start_time());
            $html[] = Translation::get('AssignmentNotStarted') . Translation::get('StartTime') . ': ' . $date;
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
        
        if ($this->assignment->get_end_time() < time() && $this->assignment->get_allow_late_submissions() == 0)
        {
            $html = array();
            
            $html[] = $this->render_header();
            $date = DatetimeUtilities::format_locale_date(
                Translation::get('DateFormatShort', null, Utilities::COMMON_LIBRARIES) . ', ' .
                     Translation::get('TimeNoSecFormat', null, Utilities::COMMON_LIBRARIES), 
                    $this->assignment->get_end_time());
            $html[] = Translation::get('AssignmentEnded') . Translation::get('EndTime') . ': ' . $date;
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
        
        return true;
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
    public function get_repo_objects()
    {
        return \Chamilo\Core\Repository\Viewer\Manager::get_selected_objects();
    }

    /**
     * Creates an assignment submission tracker
     * 
     * @param $authors string
     * @param $submitter_type int
     * @param int $submitter id
     */
    public function create_tracker($submitter_id, $repo_object, $submitter_type = null)
    {
        if (is_array($repo_object))
        {
            $repo_object = $repo_object[0];
        }
        
        $object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(), 
            $repo_object);
        
        // Create a folder assignment in the root folder
        $assignement_category_id = \Chamilo\Core\Repository\Storage\DataManager::get_repository_category_by_name_or_create_new(
            $this->assignment->get_owner_id(), 
            Translation::get("Assignments"));
        
        // Create a folder course in the assignment folder
        $course_category_id = \Chamilo\Core\Repository\Storage\DataManager::get_repository_category_by_name_or_create_new(
            $this->assignment->get_owner_id(), 
            $this->get_course()->get_visual_code() . ' - ' . $this->get_course()->get_title(), 
            $assignement_category_id);
        
        // Create a folder with the name of the assignment in the course folder
        $category_id = \Chamilo\Core\Repository\Storage\DataManager::get_repository_category_by_name_or_create_new(
            $this->assignment->get_owner_id(), 
            $this->assignment->get_title(), 
            $course_category_id);
        
        if (is_null($submitter_type))
        {
            $submitter_type = $this->get_submitter_type();
        }
        
        $copier = new ContentObjectCopier(
            $this->get_user(), 
            array($object->get_id()), 
            new PersonalWorkspace($this->get_user()), 
            $this->get_user_id(), 
            new PersonalWorkspace($this->assignment->get_owner()), 
            $this->assignment->get_owner_id(), 
            $category_id);
        $content_object_ids = $copier->run();
        
        if (count($content_object_ids) > 0)
        {
            foreach ($content_object_ids as $content_object_id)
            {
                $submitter_name = $this->get_submitter_name($submitter_type, $submitter_id);
                $new_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                    ContentObject::class_name(), 
                    $content_object_id);
                $new_object->set_title($submitter_name . ' - ' . $new_object->get_title());
                if (self::is_downloadable($new_object))
                {
                    $new_object->set_filename($submitter_name . ' - ' . $new_object->get_filename());
                }
                $new_object->update();
            }
        }
        else
        {
            foreach ($copier->get_messages() as $type)
            {
                $messages .= implode(PHP_EOL, $type);
            }
            
            $this->redirect(
                $messages, 
                true, 
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_BROWSE_SUBMISSIONS, 
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->publication_id, 
                    self::PARAM_SUBMITTER_TYPE => $submitter_type, 
                    self::PARAM_TARGET_ID => $submitter_id));
        }
        $arguments = array(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_PUBLICATION_ID => $this->publication_id, 
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_CONTENT_OBJECT_ID => $content_object_id, 
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_ID => $submitter_id, 
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_DATE_SUBMITTED => time(), 
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_TYPE => $submitter_type, 
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_USER_ID => $this->get_user_id(), 
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_IP_ADDRESS => $_SERVER['REMOTE_ADDR']);
        Event::trigger('SubmissionAssignment', \Chamilo\Application\Weblcms\Manager::context(), $arguments);
        
        return $submitter_type . $submitter_id;
    }

    /**
     * Returns the name of the submitter as a string.
     * 
     * @param $submitter_type int
     * @param $submitter_id int
     *
     * @return string The name of the submitter
     */
    private function get_submitter_name($submitter_type, $submitter_id)
    {
        switch ($submitter_type)
        {
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER :
                return \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                    \Chamilo\Core\User\Storage\DataClass\User::class_name(), 
                    $submitter_id)->get_fullname();
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP :
                return \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
                    CourseGroup::class_name(), 
                    $submitter_id)->get_name();
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP :
                return \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(Group::class_name(), $submitter_id)->get_name();
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->publication_id = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        
        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $this->get_publication_id());
        
        if (! $this->is_allowed(WeblcmsRights::VIEW_RIGHT, $publication))
        {
            $this->redirect(
                Translation::get("NotAllowed", null, Utilities::COMMON_LIBRARIES), 
                true, 
                array(), 
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION, 
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, 
                    self::PARAM_TARGET_ID, 
                    self::PARAM_SUBMITTER_TYPE));
        }
        
        $this->assignment = $publication->get_content_object();
        
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_BROWSE)), 
                Translation::get('BrowserComponent')));
        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $breadcrumbtrail->add(
                new Breadcrumb(
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_BROWSE_SUBMITTERS, 
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->publication_id)), 
                    $this->assignment->get_title()));
        }
        else
        {
            $breadcrumbtrail->add(
                new Breadcrumb(
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_STUDENT_BROWSE_SUBMISSIONS, 
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->publication_id)), 
                    $this->assignment->get_title()));
        }
    }

    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, 
            self::PARAM_TARGET_ID, 
            self::PARAM_SUBMITTER_TYPE);
    }

    /**
     * Returns whether or not the submission target is selected
     */
    protected function submissionTargetSelected()
    {
        return $this->allowGroupSubmissions() && $this->get_parameter(self::PARAM_TARGET_ID) &&
             $this->get_parameter(self::PARAM_SUBMITTER_TYPE) != AssignmentSubmission::SUBMITTER_TYPE_USER;
    }

    /**
     * Returns the selected step index
     * 
     * @return bool
     */
    protected function getSelectedStepIndex()
    {
        if ($this->allowGroupSubmissions() && ! $this->submissionTargetSelected() || ! $this->allowGroupSubmissions())
        {
            return 0;
        }
        
        return 1;
    }

    /**
     * Sends an email notification to the user to notice him of the success of his submission
     */
    protected function sendMail()
    {
        $userEmail = $this->getUser()->get_email();
        
        $title = Translation::getInstance()->getTranslation(
            'SubmissionSubmitConfirmationEmailTitle', 
            array('ASSIGNMENT_TITLE' => $this->getPublication()->get_content_object()->get_title()), 
            Manager::context());
        
        $content = array();
        
        $content[] = '<link rel="stylesheet"' .
             ' href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"' .
             ' integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7"' .
             ' crossorigin="anonymous">';
        
        $content[] = '<style>body { padding: 20px; } .assignment-success-check { display: block; font-size: 80px;' .
             ' margin: 0 auto 20px; width: 80px;}</style>';
        
        $content[] = $this->renderConfirmationMessage(
            Translation::getInstance()->getTranslation(
                'SubmissionSubmitConfirmationEmailContent', 
                array(
                    'ASSIGNMENT_TITLE' => $this->getPublication()->get_content_object()->get_title(), 
                    'COURSE' => $this->get_course()->get_title()), 
                Manager::context()));
        
        $content = implode(PHP_EOL, $content);
        
        $mail = new Mail($title, $content, $userEmail);
        
        $mailerFactory = new MailerFactory(Configuration::getInstance());
        $mailer = $mailerFactory->getActiveMailer();
        
        $mailer->sendMail($mail);
    }
}
