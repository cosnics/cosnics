<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataManager as AssignmentDataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass\Webpage;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Manages the submissions
 * 
 * @author Anthony Hurst (Hogeschool Gent)
 * @author Bert De Clercq (Hogeschool Gent)
 */
abstract class SubmissionsManager extends Manager // implements DelegateComponent
                                                  // DelegateComponent
{
    const PARAM_COUNT = 'count';
    const PARAM_FIRST_DATE = 'first_date';
    const PARAM_LAST_DATE = 'last_date';
    const TYPE_INDIVIDUAL = 'individual';
    const TYPE_COURSE_GROUP = 'course_group';
    const TYPE_GROUP = 'group';
    
    // DO NOT TOUCH. Constants for dynamic array method calls.
    private $ARRAY_FIRST = 'reset';

    private $ARRAY_PREVIOUS = 'prev';

    private $ARRAY_CURRENT = 'current';

    private $ARRAY_NEXT = 'next';

    private $ARRAY_LAST = 'end';

    private $course_groups;
    
    // modified index [submitter_type:submitter_id].
    private $platform_groups;

    private $users;
    
    // submitter instead of limited
    // numbers;
    
    // Hold 1 \application\weblcms\integration\core\tracking\tracker\AssignmentSubmission per submitter.
    private $course_group_feedback_trackers;

    private $platform_group_feedback_trackers;

    private $user_feedback_trackers;
    
    // Hold 1 \application\weblcms\integration\core\tracking\tracker\AssignmentSubmission per submitter.
    private $course_group_submission_trackers;

    private $platform_group_submission_trackers;

    private $user_submission_trackers;

    /**
     * Retrieves the publication id from the url.
     * 
     * @return int the publication id.
     */
    public function get_publication_id()
    {
        return Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }

    /**
     * Retrieves the target id from the url.
     * 
     * @return int the target id.
     */
    public function get_target_id()
    {
        return Request::get(self::PARAM_TARGET_ID);
    }

    /**
     * Retrieves the submitter type from the url.
     * 
     * @return int the submitter type.
     */
    public function get_submitter_type()
    {
        return Request::get(self::PARAM_SUBMITTER_TYPE);
    }

    /**
     * Retrieves the submission id from the url.
     * 
     * @return int The submission id.
     */
    public function get_submission_id()
    {
        return Request::get(self::PARAM_SUBMISSION);
    }

    /**
     * Retrieves the object id from the url.
     * 
     * @return int The object id.
     */
    public function get_object_id()
    {
        return Request::get(self::PARAM_OBJECT_ID);
    }

    /**
     * Returns the score tracker for the submission with the given submission id.
     * 
     * @param $submission_id int The submission id.
     * @return \application\weblcms\integration\tracking\SubmissionScore A single
     *         \application\weblcms\integration\tracking\SubmissionScore for the submission.
     */
    public function get_score_tracker_for_submission($submission_id)
    {
        $tracker = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::PROPERTY_SUBMISSION_ID), 
            new StaticConditionVariable($submission_id));
        
        return DataManager::retrieve(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::class_name(), 
            new DataClassRetrieveParameters($condition));
    }

    /**
     * Returns the feedback tracker for the submission with the given submission id.
     * 
     * @param $submission_id int The submission id.
     * @return \application\weblcms\integration\tracking\SubmissionFeedback A single
     *         \application\weblcms\integration\tracking\SubmissionFeedback for the submission.
     */
    public function get_feedback_tracker_for_submission($submission_id)
    {
        $feedback_tracker = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::PROPERTY_SUBMISSION_ID), 
            new StaticConditionVariable($submission_id));
        
        return DataManager::retrieve(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::class_name(), 
            new DataClassRetrieveParameters($condition));
    }

    /**
     * Obtains the submissions trackers of the given submitter.
     * 
     * @param $submitter_type int The type of submitter whose submission trackers are being fetched
     *        (\application\weblcms\integration\core\tracking\tracker\AssignmentSubmission::SUBMITTER_TYPE_...)
     * @param $submitter_id int The id of the submitter whose submission trackers are being fetched.
     * @return array The submission trackers found for the given submitter.
     */
    public function get_submission_trackers_by_submitter($submitter_type, $submitter_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_PUBLICATION_ID), 
            new StaticConditionVariable($this->get_publication_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_ID), 
            new StaticConditionVariable($submitter_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_TYPE), 
            new StaticConditionVariable($submitter_type));
        $condition = new AndCondition($conditions);
        $order_by = new OrderBy(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_DATE_SUBMITTED), 
            SORT_ASC);
        $this->index_array_by_id(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::get_data(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                null, 
                $condition, 
                null, 
                null, 
                $order_by)->as_array(), 
            $this->submitter_submissions_trackers);
        
        return $this->submitter_submissions_trackers;
    }

    /**
     * Returns an array of all submission trackers that belong to the publication with the given publication id.
     * 
     * @param $publication_id int The id of the publication
     * @return array The submission trackers
     */
    public function get_submission_trackers_by_publication($publication_id)
    {
        $submission_trackers = array();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_PUBLICATION_ID), 
            new StaticConditionVariable($publication_id));
        $order_by = new OrderBy(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_DATE_SUBMITTED));
        $this->index_array_by_id(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::get_data(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                null, 
                $condition, 
                null, 
                null, 
                $order_by)->as_array(), 
            $submission_trackers);
        
        return $submission_trackers;
    }

    /**
     * Retrieves the single tracker item necessary for general information for the combination submitter_type,
     * submitter_id and publication_id.
     * 
     * @param $submitter_type int the submitter type
     *        (\application\weblcms\integration\tracking\AssignmentSubmission::SUBMITTER_TYPE_...).
     * @param $submitter_id int the identity of the submitter.
     * @return \application\weblcms\integration\core\tracking\tracker\AssignmentSubmission the tracker or null if none
     *         found.
     */
    public function get_submissions_tracker($submitter_type, $submitter_id)
    {
        switch ($submitter_type)
        {
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP :
                return $this->get_submission_tracker_for_submitter(
                    $submitter_type, 
                    $submitter_id, 
                    $this->course_group_submission_trackers);
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP :
                return $this->get_submission_tracker_for_submitter(
                    $submitter_type, 
                    $submitter_id, 
                    $this->platform_group_submission_trackers);
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER :
                return $this->get_submission_tracker_for_submitter(
                    $submitter_type, 
                    $submitter_id, 
                    $this->user_submission_trackers);
        }
        
        return null;
    }

    /**
     * Retrieves the single tracker item for the combination of submitter_type, submitter_id and publication_id from the
     * cache if available, otherwise from the database.
     * 
     * @param $submitter_type int the submitter type
     *        (\application\weblcms\integration\tracking\AssignmentSubmission::SUBMITTER_TYPE_...).
     * @param $submitter_id int the identity of the submitter.
     * @param $submissions_tracker_array array the array in which the tracker is to be cached (by reference).
     * @return \application\weblcms\integration\tracking\AssignmentSubmission the tracker item retrieved from the
     *         database.
     */
    private function get_submission_tracker_for_submitter($submitter_type, $submitter_id, &$submissions_tracker_array)
    {
        if (count($submissions_tracker_array) == 0)
        {
            $submissions_trackers = $this->get_submissions_trackers_for_submitter_type($submitter_type);
            $this->index_array_by_submitter_id($submissions_trackers, $submissions_tracker_array);
        }
        
        return $submissions_tracker_array[$submitter_id];
    }

    /**
     * Retrieves the submissions trackers from the database for the given submitter type.
     * 
     * @param $submitter_type int the submitter type
     *        (\application\weblcms\integration\tracking\AssignmentSubmission::SUBMITTER_TYPE_...).
     * @return array an array of trackers.
     */
    private function get_submissions_trackers_for_submitter_type($submitter_type)
    {
        return AssignmentDataManager::retrieve_submissions_by_submitter_type(
            $this->get_publication_id(), 
            $submitter_type, 
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name())->as_array();
    }

    /**
     * Indexes an indexless array by the submitter id of the trackers.
     * 
     * @param $trackers array the trackers to be indexed.
     * @param $tracker_array array the array into which the index and trackers are to be placed.
     */
    private function index_array_by_submitter_id($trackers, &$tracker_array)
    {
        foreach ($trackers as $tracker)
        {
            $tracker_array[$tracker[AssignmentSubmission::PROPERTY_SUBMITTER_ID]] = $tracker;
        }
    }

    /**
     * Retrieves the single tracker item necessary for general information for the combination submitter type, submitter
     * id and submission id.
     * 
     * @param $submitter_type int the submitter type
     *        (\application\weblcms\integration\tracking\AssignmentSubmission::SUBMITTER_TYPE_...)
     * @param $submitter_id int the identity of the submitter.
     * @return \application\weblcms\integration\tracking\SubmissionFeedback The single feedback tracker found, or null
     *         if none found.
     */
    public function get_submitter_feedbacks($submitter_type, $submitter_id)
    {
        switch ($submitter_type)
        {
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP :
                return $this->get_feedback_tracker_for_submitter(
                    $submitter_type, 
                    $submitter_id, 
                    $this->course_group_feedback_trackers);
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP :
                return $this->get_feedback_tracker_for_submitter(
                    $submitter_type, 
                    $submitter_id, 
                    $this->platform_group_feedback_trackers);
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER :
                return $this->get_feedback_tracker_for_submitter(
                    $submitter_type, 
                    $submitter_id, 
                    $this->user_feedback_trackers);
        }
        
        return null;
    }

    /**
     * Retrieves the feedback tracker for the combination submitter type, submitter id and submission id from the
     * database.
     * 
     * @param $submission_id int the id of the submission for which the information is being retrieved.
     * @return \application\weblcms\integration\core\tracking\tracker\SubmissionFeedback the tracker or null if none
     *         found.
     */
    private function get_feedback_tracker_for_submitter($submitter_type, $submitter_id, &$feedback_tracker_array)
    {
        if (count($feedback_tracker_array) == 0)
        {
            $feedback_trackers = $this->get_feedback_trackers_for_submitter_type($submitter_type);
            $this->index_array_by_submitter_id($feedback_trackers, $feedback_tracker_array);
        }
        
        return $feedback_tracker_array[$submitter_id];
    }

    /**
     * Retrieves feedback information from the database.
     * 
     * @param $submitter_type int the submitter type
     *        (\application\weblcms\integration\tracking\AssignmentSubmission::SUBMITTER_TYPE_...).
     * @return array an array of feedback information.
     */
    private function get_feedback_trackers_for_submitter_type($submitter_type)
    {
        return AssignmentDataManager::retrieve_submitter_feedbacks(
            $this->get_publication_id(), 
            $submitter_type, 
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name())->as_array();
    }

    /**
     * Add an idditional breadcrumb to the trail.
     * 
     * @param $breadcrumb_trail BreadcrumbTrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumb_trail)
    {
        $breadcrumb_trail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_BROWSE), 
                    array(self::PARAM_TARGET_ID, self::PARAM_PUBLICATION_ID, self::PARAM_SUBMITTER_TYPE)), 
                Translation::get('BrowserComponent')));
    }

    /**
     * Generates standard HTML for the display of attachments.
     * 
     * @param $attachment mixed The attachment for which HTML is to be generated.
     * @return string the html for the standard way of displaying attachments.
     */
    public function generate_attachment_placeholder($attachment, $type = null)
    {
        $html = array();
        if (self::is_downloadable($attachment))
        {
            $download_url = \Chamilo\Core\Repository\Manager::get_document_downloader_url($attachment->get_id());
            
            $html[] = '<a href="' . $download_url . '">';
            $html[] = '<img src="' . Theme::getInstance()->getCommonImagePath('Action/Download') . '" title="' . Translation::get(
                'Download') . '"/>';
            $html[] = '</a>';
        }
        else
        {
            $html[] = '<a>';
            $html[] = '<img src="' . Theme::getInstance()->getCommonImagePath('Action/DownloadNa') . '" title="' . Translation::get(
                'DownloadNotPossible') . '"/>';
            $html[] = '</a>';
        }
        $html[] = '<img src="' . $attachment->get_icon_path(Theme::ICON_MINI) . '" alt="' . htmlentities(
            Translation::get(
                'TypeName', 
                array(), 
                ClassnameUtilities::getInstance()->getNamespaceFromClassname($attachment->get_type()))) . '"/>';
        $html[] = '<a onclick="javascript:openPopup(\'' . $this->generate_attachment_viewer_url($attachment, $type) .
             '\'); return false;" href="#">';
        $html[] = $attachment->get_title();
        $html[] = '</a>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Generates the url necessary to launch the viewer component to view the attachment.
     * 
     * @param $attachment ContentObject The attachment for which the viewer url is to be generated.
     * @param $type string The attachment type.
     * @return String the viewer url of the given attachment.
     */
    public function generate_attachment_viewer_url($attachment, $type = null)
    {
        return str_replace(
            '\\', 
            '\\\\', 
            urldecode(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW_ATTACHMENT, 
                        self::PARAM_PUBLICATION_ID => $this->get_publication_id(), 
                        self::PARAM_OBJECT_ID => $attachment->get_id(), 
                        self::PARAM_ATTACHMENT_TYPE => $type))));
    }

    /**
     * Checks whether the given content object is downloadable.
     * 
     * @param $content_object ContentObject The content object to check
     * @return boolean True if the content object is downloadable
     */
    public static function is_downloadable($content_object)
    {
        if (self::is_document($content_object))
        {
            return true;
        }
        
        return false;
    }

    /**
     * Checks whether the given content object is a document.
     * 
     * @param $content_object ContentObject The content object to check
     * @return boolean True if the content object is a document
     */
    protected static function is_document($content_object)
    {
        if ($content_object instanceof File || $content_object instanceof Webpage)
        {
            return true;
        }
        
        return false;
    }

    /**
     * Generates HTML that displays the details of the current assignment.
     * 
     * @return string the HTML for display.
     */
    public function generate_assignment_details_html()
    {
        $html = array();
        
        // Title
        $html[] = '<div class="panel-heading">';
        $html[] = '<h5 class="panel-title">';
        $html[] = Translation::get('Details');
        $html[] = '</h5></div>';
        $html[] = '<div class="panel-body">';
        
        // Time titles
        $html[] = '<div style="font-weight:bold;float:left;">';
        $html[] = Translation::get('StartTime') . ':&nbsp;<br />';
        $html[] = Translation::get('EndTime') . ':&nbsp;<br />';
        $html[] = '</div>';
        
        // Times
        $html[] = '<div style="float:left;">';
        $html[] = DatetimeUtilities::format_locale_date(
            Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES), 
            $this->get_assignment()->get_start_time()) . '<br />';
        $html[] = DatetimeUtilities::format_locale_date(
            Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES), 
            $this->get_assignment()->get_end_time()) . '<br />';
        $html[] = '<br /></div><br />';
        $html[] = '<div class="clearfix"></div>';
        
        // Description title
        $html[] = '<div class="description" style="font-weight:bold;">';
        $html[] = Translation::get('Description');
        $html[] = '</div>';
        
        // Description
        $html[] = '<div class="description">';
        $html[] = $this->get_assignment()->get_description();
        $html[] = '</div>';
        
        // Attachments
        $attachments = $this->get_assignment()->get_attachments();
        if (count($attachments) > 0)
        {
            $html[] = '<div class="description" style="font-weight:bold;">';
            $html[] = Translation::get('Attachments');
            $html[] = '</div>';
            
            Utilities::order_content_objects_by_title($attachments);
            
            $html[] = '<div class="description">';
            $html[] = '<ul>';
            foreach ($attachments as $attachment)
            {
                $html[] = '<li>' . $this->generate_attachment_placeholder($attachment) . '</li>';
            }
            $html[] = '</ul>';
            $html[] = '</div>';
        }
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Generates HTML for automatic feedback for the current assignment.
     * 
     * @return string The HTML for the automatic feedback
     */
    public function generate_automatic_feedback_html()
    {
        $html = array();
        
        if ($this->get_assignment()->get_automatic_feedback_text())
        {
            $html[] = '<br />';
            $html[] = $this->get_assignment()->get_automatic_feedback_text() . '<br />';
        }
        
        if (! is_null($this->get_assignment()->get_automatic_feedback_co_ids()))
        {
            $html[] = '<ul>';
            
            foreach (explode(',', $this->get_assignment()->get_automatic_feedback_co_ids()) as $content_object_id)
            {
                if (\Chamilo\Core\Repository\Storage\DataManager::count_content_objects(
                    ContentObject::class_name(), 
                    new DataClassCountParameters(
                        new EqualityCondition(
                            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID), 
                            new StaticConditionVariable($content_object_id)))))
                {
                    $html[] = '<li>' . $this->generate_attachment_placeholder(
                        \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                            ContentObject::class_name(), 
                            $content_object_id), 
                        AttachmentViewerComponent::TYPE_AUTOMATIC_FEEDBACK) . '</li>';
                }
            }
            
            $html[] = '</ul>';
        }
        else
        {
            $html[] = '<br />';
        }
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Retrieves a submitter by their id and submitter type.
     * 
     * @param $submitter_type int The type of the submitter to be retrieved.
     * @param $submitter_id int The id of the submitter to be retrieved.
     * @return mixed the submitter or null if not found.
     */
    public function get_submitter($submitter_type, $submitter_id)
    {
        switch ($submitter_type)
        {
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP :
                return $this->get_submitter_by_submitter_id($submitter_type, $submitter_id, $this->course_groups);
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP :
                return $this->get_submitter_by_submitter_id($submitter_type, $submitter_id, $this->platform_groups);
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER :
                return $this->get_submitter_by_submitter_id($submitter_type, $submitter_id, $this->users);
        }
    }

    /**
     * Retrieves a submitter from the given array, or the database if the array is empty.
     * 
     * @param $submitter_type int The type of the submitter being retrieved.
     * @param $submitter_id int The id of the submitter being retrieved.
     * @param $submitters_array array The array in which the submitter should be found.
     * @return mixed the submitter or null if not found.
     */
    private function get_submitter_by_submitter_id($submitter_type, $submitter_id, &$submitters_array)
    {
        if (count($submitters_array) == 0)
        {
            $submitters = $this->get_submitters($submitter_type);
            $this->index_array_by_id($submitters, $submitters_array);
        }
        
        return $submitters_array[$submitter_id];
    }

    /**
     * Indexes an array by the identities of the passed items.
     * 
     * @param $items array The items to be indexed.
     * @param $items_array array The array into which the items are to be placed.
     * @param $id_method string The method used to obtain the id of the item. Assumed to be get_id() if not passed.
     */
    private function index_array_by_id($items, &$items_array, $id_method = 'get_id')
    {
        foreach ($items as $item)
        {
            $items_array[$item->$id_method()] = $item;
        }
    }

    /**
     * Retrieves submitters from the database based on their submitter type.
     * 
     * @param $submitter_type int The type of the submitters to be retrieved from the database.
     * @return array an array of submitters.
     */
    private function get_submitters($submitter_type)
    {
        switch ($submitter_type)
        {
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP :
                $order_property = new OrderBy(
                    new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_NAME));
                
                return \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_publication_target_course_groups(
                    $this->get_publication_id(), 
                    $this->get_course_id(), 
                    null, 
                    null, 
                    $order_property)->as_array();
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP :
                
                $order_property = new OrderBy(
                    new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME), 
                    SORT_ASC, 
                    \Chamilo\Core\Group\Storage\DataManager::get_alias(Group::get_table_name()));
                
                return \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_publication_target_platform_groups(
                    $this->get_publication_id(), 
                    $this->get_course_id(), 
                    null, 
                    null, 
                    $order_property)->as_array();
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER :
                
                $order_property = new OrderBy(
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME), 
                    SORT_ASC, 
                    \Chamilo\Core\User\Storage\DataManager::get_alias(User::get_table_name()));
                
                return \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_publication_target_users(
                    $this->get_publication_id(), 
                    $this->get_course_id(), 
                    null, 
                    null, 
                    $order_property)->as_array();
        }
    }

    /**
     * Obtains the id of the preceding submitter.
     * 
     * @param $submitter_type int The type of the submitter.
     * @param $current_submitter_id int The id of the submitter who is the reference point.
     * @return int the id of the previous submitter or null if none found.
     */
    public function get_previous_submitter_information($submitter_type, $current_submitter_id)
    {
        return $this->get_position_submitter_information($submitter_type, $current_submitter_id, $this->ARRAY_PREVIOUS);
    }

    /**
     * Obtains the id of the following submitter.
     * 
     * @param $submitter_type int The type of submitter.
     * @param $surrent_submitter_id int The id of the sibmitter who is the reference point.
     * @return int the id of the next submitter or null if none found.
     */
    public function get_next_submitter_information($submitter_type, $current_submitter_id)
    {
        return $this->get_position_submitter_information($submitter_type, $current_submitter_id, $this->ARRAY_NEXT);
    }

    /**
     * Gets the position of as submitter within the list of submitters for an assignment.
     * 
     * @param $submitter_type int The type of the submitter.
     * @param $submitter_id int The id of the submitter.
     * @return int the position of the submitter within the current submitters. -1 if not present in the list.
     */
    public function get_position_submitter($submitter_type, $submitter_id)
    {
        switch ($submitter_type)
        {
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP :
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP :
                $index = $submitter_type . ':' . $submitter_id;
                
                return $this->get_index_position_item($index, $this->groups);
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER :
                return $this->get_index_position_item($submitter_id, $this->users);
        }
    }

    /**
     * Obtains the position of the submitter in their list in relation to those with submissions for the current
     * assignment.
     * 
     * @param $submitter_type int The type of the submitter.
     * @param $submitter_id int The id of the submitter.
     * @return int The position of the submitter in their list.
     */
    public function get_position_submitter_with_submissions($submitter_type, $submitter_id)
    {
        switch ($submitter_type)
        {
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP :
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP :
                $index = $submitter_type . ':' . $submitter_id;
                
                return $this->get_index_position_submitter_with_submissions($index, $this->groups);
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER :
                return $this->get_index_position_submitter_with_submissions($submitter_id, $this->users);
        }
    }

    /**
     * Obtains the position of the index within the given array in relation to those with submissions for the current
     * assignment.
     * 
     * @param $index mixed The index to be searched for (Key in the given array).
     * @param $items_array array The array to be searched.
     * @return int The position in which the index was found, or -1 if the index is not found.
     */
    private function get_index_position_submitter_with_submissions($index, &$items_array)
    {
        if (! array_key_exists($index, $items_array))
        {
            return - 1;
        }
        $index_position = 1;
        $current_item = reset($items_array);
        while (key($items_array) != $index && $current_item)
        {
            $submitter_type = $this->determine_submitter_type($current_item);
            if ($this->get_submissions_tracker($submitter_type, $current_item->get_id()))
            {
                $index_position ++;
            }
            $current_item = next($items_array);
        }
        
        return $index_position;
    }

    /**
     * Gets the position of an index within a given array (ordinal).
     * 
     * @param $index mixed The index being sought.
     * @param $items_array array The array of items being searched.
     * @return int the position within the array in which the index was encountered. -1 if index not found.
     */
    private function get_index_position_item($index, &$items_array)
    {
        $index_position = 1;
        $current_item = reset($items_array);
        while (key($items_array) != $index && $current_item)
        {
            $index_position ++;
            $current_item = next($items_array);
        }
        if ($index_position > count($items_array))
        {
            return - 1;
        }
        
        return $index_position;
    }

    /**
     * Counts the number of submitters for the assignment.
     * 
     * @return int the number of current submissions. 0 if no submitters have been retrieved for the current assignment.
     */
    public function get_count_submitters($submitter_type)
    {
        switch ($submitter_type)
        {
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP :
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP :
                return count($this->groups);
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER :
                return count($this->users);
        }
    }

    /**
     * Gets the position of the submission within the list of current submissions.
     * Does not instantiate the list of
     * current submissions.
     * 
     * @param int The id of the submission whose position is to be determined.
     * @return int The position of the submission within the list of the current submitter's submissions.
     */
    public function get_position_submissions($submission_id)
    {
        return $this->get_index_position_item($submission_id, $this->submitter_submissions_trackers);
    }

    /**
     * Counts the number of current submissions.
     * 
     * @return int the number of current submissions. 0 if no submissions have been retrieved for the current submitter.
     */
    public function get_count_submissions()
    {
        return count($this->submitter_submissions_trackers);
    }

    /**
     * Obtains the id of the submitter in the given position relative to the reference point.
     * 
     * @param $submitter_type int The type of submitter.
     * @param $current_submitter_id int The id of the reference submitter.
     * @param $array_method string The position to be looked at ($this->ARRAY_...).
     * @return int the id of the submitter found at the requested position or null if none found.
     */
    private function get_position_submitter_information($submitter_type, $current_submitter_id, $array_method)
    {
        $submitter = null;
        $this->get_submitter($submitter_type, $current_submitter_id);
        if ($submitter_type !=
             \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER &&
             count($this->groups) == 0)
        {
            $this->get_submitter(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP, 
                $current_submitter_id);
            $this->get_submitter(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP, 
                $current_submitter_id);
            $this->populate_groups_field();
        }
        
        switch ($submitter_type)
        {
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP :
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP :
                $index = $submitter_type . ':' . $current_submitter_id;
                $submitter = $this->get_position_item($index, $this->groups, $array_method);
                break;
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER :
                $submitter = $this->get_position_item($current_submitter_id, $this->users, $array_method);
                break;
        }
        
        if ($submitter)
        {
            $submitter_type = $this->determine_submitter_type($submitter);
            $submission_tracker = $this->retrieve_latest_submission_by_submitter($submitter_type, $submitter->get_id());
            $submission_tracker_id = null;
            if ($submission_tracker)
            {
                $submission_tracker_id = $submission_tracker->get_id();
            }
            
            return array(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_ID => $submitter->get_id(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_TYPE => $submitter_type, 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_ID => $submission_tracker_id);
        }
        
        return null;
    }

    /**
     * Determines the \application\weblcms\integration\tracking\AssignmentSubmission constant applicable to the
     * submitter
     * 
     * @param $submitter mixed The submitter whose type is to be determined.
     * @return int the \application\weblcms\integration\tracking\AssignmentSubmission constant applicable to the
     *         submitter. Null if not a submitter.
     */
    public function determine_submitter_type($submitter)
    {
        if ($submitter instanceof CourseGroup)
        {
            return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP;
        }
        if ($submitter instanceof Group)
        {
            return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP;
        }
        if ($submitter instanceof User)
        {
            return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER;
        }
        
        return null;
    }

    /**
     * Retrieves the latest submission of a given submitter for the current assignment.
     * 
     * @param $submitter_type int The type of submitter.
     * @param $submitter_id int The id of the submitter.
     * @return \application\weblcms\integration\tracking\AssignmentSubmission the tracker for the latest submission or
     *         null if none found.
     */
    private function retrieve_latest_submission_by_submitter($submitter_type, $submitter_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_PUBLICATION_ID), 
            new StaticConditionVariable($this->get_publication_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_ID), 
            new StaticConditionVariable($submitter_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_TYPE), 
            new StaticConditionVariable($submitter_type));
        $condition = new AndCondition($conditions);
        $order_by = new OrderBy(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_DATE_SUBMITTED));
        $submissions_trackers = \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::get_data(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
            null, 
            $condition, 
            0, 
            1, 
            $order_by)->as_array();
        
        return $submissions_trackers[0];
    }

    /**
     * Populates the groups field alphabetically with the contents of the fields course_groups and platform_groups.
     */
    private function populate_groups_field()
    {
        $insert_arrays = array($this->course_groups, $this->platform_groups);
        $groups = array();
        foreach ($insert_arrays as $insert_array)
        {
            if (count($groups) == 0)
            {
                $groups = array_merge($insert_array);
                continue;
            }
            $position_groups = 0;
            $position_insert_array = 0;
            $current_group = reset($groups);
            $current_insert_array = reset($insert_array);
            while ($current_group || $current_insert_array)
            {
                if (! $current_insert_array) // no more elements to add
                {
                    break;
                }
                elseif (! $current_group) // no more elements in the array being
                                         // added to
                {
                    array_splice($groups, count($groups), 0, array_slice($insert_array, $position_insert_array));
                    break;
                }
                if (strcasecmp($current_group->get_name(), $current_insert_array->get_name()) > 0)
                {
                    array_splice($groups, $position_groups, 0, array($current_insert_array));
                    $position_groups ++;
                    $position_insert_array ++;
                    $current_insert_array = next($insert_array);
                    continue;
                }
                $position_groups ++;
                $current_group = next($groups);
            }
        }
        foreach ($groups as $group)
        {
            $submitter_type = null;
            if ($group instanceof CourseGroup)
            {
                $submitter_type = \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP;
            }
            elseif ($group instanceof Group)
            {
                $submitter_type = \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP;
            }
            $index = $submitter_type . ':' . $group->get_id();
            $this->groups[$index] = $group;
        }
    }

    /**
     * Obtains the item at the given position within the given array.
     * Relies on the index of the array being the id of
     * the items.
     * 
     * @param $reference_item_id mixed The id of the item at the reference point.
     * @param $item_array array The array to be searched.
     * @param $array_method string The position to be found.
     * @return mixed the item found at the given position or null if not found.
     */
    private function get_position_item($reference_item_id, &$item_array, $array_method)
    {
        switch ($array_method)
        {
            case $this->ARRAY_FIRST :
            case $this->ARRAY_LAST :
                return $array_method($item_array);
            default :
                break;
        }
        
        $current_item = reset($item_array);
        
        while ($current_item)
        {
            if (key($item_array) == $reference_item_id)
            {
                return $array_method($item_array);
            }
            $current_item = next($item_array);
        }
    }

    /**
     * Retrieves the id of the previous submission relative to the current submission.
     * 
     * @param $submitter_type int The submitter type of the current submitter
     *        (\application\weblcms\integration\core\tracking\tracker\AssignmentSubmission::SUBMITTER_TYPE_...).
     * @param $current_submitter_id int The id of the current submitter.
     * @param $current_submission_id int The id of the current submission.
     */
    public function get_later_submission_information($submitter_type, $current_submitter_id, $current_submission_id)
    {
        return $this->get_position_submission_information(
            $submitter_type, 
            $current_submitter_id, 
            $current_submission_id, 
            $this->ARRAY_NEXT);
    }

    /**
     * Retrieves the id of the next submission relative to the current submission.
     * 
     * @param $submitter_type int The submitter type of the current submitter
     *        (\application\weblcms\integration\core\tracking\tracker\AssignmentSubmission::SUBMITTER_TYPE_...).
     * @param $current_submitter_id int The id of the current submitter.
     * @param $current_submission_id int The id of the current submission.
     */
    public function get_earlier_submission_information($submitter_type, $current_submitter_id, $current_submission_id)
    {
        return $this->get_position_submission_information(
            $submitter_type, 
            $current_submitter_id, 
            $current_submission_id, 
            $this->ARRAY_PREVIOUS);
    }

    /**
     * Retrieves the identity of the submission tracker in the indicated position relative to the passed reference
     * point.
     * 
     * @param $submitter_type int The submitter type of the submitter
     *        (\application\weblcms\integration\core\tracking\tracker\AssignmentSubmission::SUBMITTER_TYPE_...).
     * @param $current_submitter_id int The id of the current submitter.
     * @param $current_submission_id int The id of the current submission. Reference point.
     * @param $array_method string The position required relative to the reference point.
     * @return int the id of the requested submission or null if there isn't one.
     */
    private function get_position_submission_information($submitter_type, $current_submitter_id, $current_submission_id, 
        $array_method)
    {
        if (! $this->are_current_submitter_submissions_trackers($submitter_type, $current_submitter_id))
        {
            $this->get_submission_trackers_by_submitter($submitter_type, $current_submitter_id);
        }
        $found_submissions_tracker = $this->get_position_item(
            $current_submission_id, 
            $this->submitter_submissions_trackers, 
            $array_method);
        if ($found_submissions_tracker)
        {
            return $found_submissions_tracker->get_id();
        }
        
        return null;
    }

    /**
     * Determines whether the trackers held in $this->submitter_submissions_trackers belong to the given submitter.
     * 
     * @param $submitter_type int The submitter type of the submitter
     *        (\application\weblcms\integration\core\tracking\tracker\AssignmentSubmission::SUBMITTER_TYPE_...).
     * @param $submitter_id int The id of the submitter.
     * @return boolean whether the trackers belong to the given submitter.
     */
    private function are_current_submitter_submissions_trackers($submitter_type, $submitter_id)
    {
        if (count($this->submitter_submissions_trackers) == 0)
        {
            return false;
        }
        $first_tracker = reset($this->submitter_submissions_trackers);
        
        return $first_tracker->get_submitter_id() != $submitter_id ||
             $first_tracker->get_submitter_type() != $submitter_type;
    }

    /**
     * Checks whether the feedback from an assignment should be visible or not.
     * 
     * @param $assignment Assignment The assignment to check
     * @param $has_submissions boolean If the assignment already has submissions or not
     * @return boolean Returns true if the feedback is visible and false otherwise
     */
    public function is_feedback_visible($assignment, $has_submissions)
    {
        if (is_null($assignment->get_visibility_feedback()))
        {
            return false;
        }
        
        switch ($assignment->get_visibility_feedback())
        {
            case Assignment::VISIBILITY_FEEDBACK_AFTER_END_TIME :
                return (time() > $assignment->get_end_time());
            
            case Assignment::VISIBILITY_FEEDBACK_AFTER_SUBMISSION :
                return $has_submissions;
            
            default :
                return false;
        }
    }

    protected function get_submitters_type_by_name($name)
    {
        $mapping = array(
            self::TYPE_INDIVIDUAL => AssignmentSubmission::SUBMITTER_TYPE_USER, 
            self::TYPE_COURSE_GROUP => AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP, 
            self::TYPE_GROUP => AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP);
        
        return $mapping[$name];
    }
}
