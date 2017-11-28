<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionNote;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionDetail\SubmissionDetailFeedbackSection;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionDetail\SubmissionDetailGeneralInfoSection;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionDetail\SubmissionDetailNotesSection;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionDetail\SubmissionDetailScoreSection;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\Document\Document;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Tabs\DynamicActionsTab;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This component allows a user to view the details of a single submission.
 *
 * @author Bert De Clercq (Hogeschool Gent)
 * @author Anthony Hurst (Hogeschool Gent)
 */
class SubmissionViewerComponent extends SubmissionsManager
{

    /**
     * The submission of which to show the details.
     *
     * @var Document
     */
    private $submission;

    private $assignment;

    private $score_section;

    private $feedback_section;

    private $notes_section;

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * Runs this submission viewer component.
     */
    function run()
    {
        $this->validate_forms();
        $this->test_view_rights();
        $this->change_last_breadcrumb();

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->display_submission_details();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * This function checks whether or not a user has permission to access this page.
     */
    private function test_view_rights()
    {
        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(),
            $this->get_publication_id()
        );

        $this->assignment = $publication->get_content_object();

        if (!$this->is_allowed(WeblcmsRights::VIEW_RIGHT, $publication) ||
            !$this->is_allowed(WeblcmsRights::VIEW_RIGHT))
        {
            throw new NotAllowedException();
        }

        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT) || $this->assignment->get_visibility_submissions())
        {
            return;
        }

        $is_member = false;
        switch ($this->get_submitter_type())
        {
            case AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP :
                $is_member = $this->is_course_group_member($this->get_target_id(), $this->get_user_id());
                break;
            case AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP :
                $is_member = $this->is_platform_group_member($this->get_target_id(), $this->get_user_id());
                break;
            case AssignmentSubmission::SUBMITTER_TYPE_USER :
                $is_member = $this->is_current_user($this->get_target_id());
                break;
        }

        if (!$is_member)
        {
            throw new NotAllowedException();
        }
    }

    /**
     * Returns true if the user with given user id is a member of the course group with the given group id.
     *
     * @param $group_id int
     * @param $user_id int
     *
     * @return boolean
     */
    private function is_course_group_member($group_id, $user_id)
    {
        return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager::is_course_group_member(
            $group_id,
            $user_id
        );
    }

    /**
     * Returns true if the user with the given user id is a member of the platform group with the given group id.
     *
     * @param $group_id type
     * @param $user_id type
     *
     * @return boolean
     */
    private function is_platform_group_member($group_id, $user_id)
    {
        $group = \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(Group::class_name(), $group_id);

        if (\Chamilo\Core\Group\Storage\DataManager::is_group_member($group_id, $user_id))
        {
            return true;
        }

        if ($group->has_children())
        {
            foreach ($group->get_subgroups() as $subgroup)
            {
                if ($this->is_platform_group_member($subgroup->get_id(), $user_id))
                {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns true when the user that is logged in is the same as the user with the given user id, and false otherwise.
     *
     * @param $user_id int
     *
     * @return boolean
     */
    private function is_current_user($user_id)
    {
        return $this->get_user_id() == $user_id;
    }

    /**
     * Assigns a value to all the caching variables.
     */
    private function find_data()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(AssignmentSubmission::class_name(), AssignmentSubmission::PROPERTY_ID),
            new StaticConditionVariable(Request::get(self::PARAM_SUBMISSION))
        );

        // Submission
        $submissions = AssignmentSubmission::get_data(AssignmentSubmission::class_name(), null, $condition)->as_array();

        if ($submissions[0])
        {
            $this->submission = $submissions[0]->get_content_object();
        }
    }

    /**
     * Returns the submission.
     *
     * @return Document The submission
     */
    function get_submission()
    {
        return $this->submission;
    }

    /**
     * Returns the assignment for the submission.
     *
     * @return Assignment The assignment
     */
    function get_assignment()
    {
        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(),
            $this->get_publication_id()
        );

        return $publication->get_content_object();
    }

    /**
     * Returns a submission tracker of the submission currently being viewed.
     * @return AssignmentSubmission The submission tracker
     * @throws NoObjectSelectedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    function get_submission_tracker()
    {
        $submissionTranslation = Translation::getInstance()->getTranslation('Submission');

        $submissionId = $this->getRequest()->get(self::PARAM_SUBMISSION);
        if (empty($submissionId))
        {
            throw new NoObjectSelectedException($submissionTranslation);
        }

        $condition = new EqualityCondition(
            new PropertyConditionVariable(AssignmentSubmission::class_name(), AssignmentSubmission::PROPERTY_ID),
            new StaticConditionVariable($submissionId)
        );

        $submission =
            DataManager::retrieve(AssignmentSubmission::class_name(), new DataClassRetrieveParameters($condition));

        if (!$submission instanceof AssignmentSubmission)
        {
            throw new ObjectNotExistException($submissionTranslation, $submissionId);
        }

        return $submission;
    }

    /**
     * Returns a score tracker of the submission currently being viewed.
     *
     * @return SubmissionScore The score tracker
     */
    function get_score_tracker()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(SubmissionScore::class_name(), SubmissionScore::PROPERTY_SUBMISSION_ID),
            new StaticConditionVariable(Request::get(self::PARAM_SUBMISSION))
        );

        return DataManager::retrieve(SubmissionScore::class_name(), new DataClassRetrieveParameters($condition));
    }

    /**
     * Returns feedback trackers of the submission currently being viewed.
     *
     * @return array The feedback trackers
     */
    function get_feedback_trackers()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(SubmissionFeedback::class_name(), SubmissionFeedback::PROPERTY_SUBMISSION_ID),
            new StaticConditionVariable(Request::get(self::PARAM_SUBMISSION))
        );

        return DataManager::retrieves(SubmissionFeedback::class_name(), new DataClassRetrievesParameters($condition));
    }

    /**
     * Returns a note tracker of the submission currently being viewed.
     *
     * @return SubmissionNote The note tracker
     */
    function get_note_tracker()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(SubmissionNote::class_name(), SubmissionNote::PROPERTY_SUBMISSION_ID),
            new StaticConditionVariable(Request::get(self::PARAM_SUBMISSION))
        );

        return DataManager::retrieve(SubmissionNote::class_name(), new DataClassRetrieveParameters($condition));
    }

    /**
     * Returns the name of the submitter as a string.
     * When submitted as a group, it will return the name of the user who
     * submitted followed by the group name.
     *
     * @return string The name of the submitter
     */
    function get_submitter_name()
    {
        // name of the user who submitted
        $user_name = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User::class_name(),
            $this->get_submission_tracker()->get_user_id()
        );

        switch ($this->get_submitter_type())
        {
            case AssignmentSubmission::SUBMITTER_TYPE_USER :
                return $user_name->get_fullname();
            case AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP :
            {
                $courseGroup =
                    \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager::retrieve_by_id(
                        CourseGroup::class_name(),
                        $this->get_target_id()
                    );

                if ($courseGroup instanceof CourseGroup)
                {
                    return $courseGroup->get_name();
                }

                return Translation::getInstance()->getTranslation('SubmitterUnknown', array(), Manager::context());
            }
            case AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP :
                return \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(
                    Group::class_name(),
                    $this->get_target_id()
                )->get_name();
        }
    }

    /**
     * Returns additional parameters as an array.
     *
     * @return array The additional parameters
     */
    function get_additional_parameters()
    {
        return array(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID,
            self::PARAM_TARGET_ID,
            self::PARAM_SUBMITTER_TYPE,
            self::PARAM_SUBMISSION
        );
    }

    /**
     * Validates the different forms on the page.
     */
    private function validate_forms()
    {
        $this->score_section = new SubmissionDetailScoreSection($this->get_score_tracker(), $this->get_url());
        $this->feedback_section = new SubmissionDetailFeedbackSection(
            $this->get_submission_tracker(),
            $this,
            $this->get_url()
        );
        $this->notes_section = new SubmissionDetailNotesSection($this->get_note_tracker(), $this->get_url());

        // Score section
        if ($this->score_section->validate())
        {

            $this->score_section->set_score();
            $this->redirect(Translation::get('ScoreUpdated'), null, $this->get_parameters());
        }

        // Feedback section
        if ($this->feedback_section->validate())
        {
            $this->feedback_section->set_feedback();
            $this->redirect(Translation::get('FeedbackCreated'), null, $this->get_parameters());
        }

        // Note section
        if ($this->notes_section->validate())
        {

            $this->notes_section->set_note();
            $this->redirect(Translation::get('NoteUpdated'), null, $this->get_parameters());
        }
    }

    /**
     * Displays the details of the submission.
     */
    private function display_submission_details()
    {
        $html = array();

        $html[] = $this->display_navigation_bar();

        $tabs = new DynamicTabsRenderer('admin');
        $general_info_section = new SubmissionDetailGeneralInfoSection($this);

        $tabs->add_tab(
            new DynamicContentTab(
                'general_info',
                Translation::get('General'),
                new FontAwesomeGlyph('file-text-o'),
                $general_info_section->get_section_content()
            )
        );

        $tabs->add_tab(
            new DynamicContentTab(
                'score',
                Translation::get('Score'),
                new FontAwesomeGlyph('bar-chart'),
                $this->get_score_section_content()
            )
        );

        $tabs->add_tab(
            new DynamicContentTab(
                'feedback',
                Translation::get('Feedback'),
                new FontAwesomeGlyph('inbox'),
                $this->get_feedback_section_content()
            )
        );

        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $tabs->add_tab(
                new DynamicContentTab(
                    'notes',
                    Translation::get('PersonalNote'),
                    new FontAwesomeGlyph('sticky-note-o'),
                    $this->get_notes_section_content()
                )
            );
        }

//        $html[] = $tabs->render();

        $html[] = $general_info_section->render_section();
        $html[] = $this->display_section('Score', $this->get_score_section_content());
        $html[] = $this->display_section('Feedback', $this->get_feedback_section_content());
        $html[] = $this->display_section('Notes', $this->get_notes_section_content());

        return implode(PHP_EOL, $html);
    }

    /**
     * Displays the navigation bar.
     */
    private function display_navigation_bar()
    {
        $html = array();

//        $html[] = '<div class="announcements level_2" style="background-image:url(' .
//            Theme::getInstance()->getCommonImagePath('ContentObject/Introduction') . ';width=100%;">';
        $html[] = '<div class="row">';
        if ($this->assignment->get_visibility_submissions() || $this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $html[] = $this->generate_submitters_navigator();
        }
        $html[] = $this->generate_submissions_navigator();
        $html[] = '</div>';
//        $html[] = '</div>';
//        $html[] = '<div class="clear">&nbsp;</div><br/>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Gets the url associated with the previous submission submitted earlier (when ordered chronologically).
     *
     * @return type the url associated with the submission submitted earlier or null if none found.
     */
    private function get_earlier_submission_url()
    {
        $earlier_submission_information = $this->get_earlier_submission_information(
            $this->get_submitter_type(),
            $this->get_target_id(),
            $this->get_submission_id()
        );
        if ($earlier_submission_information)
        {
            return $this->get_url(array(self::PARAM_SUBMISSION => $earlier_submission_information));
        }

        return null;
    }

    /**
     * Gets the url associated with the next submission submitted later (when ordered chronologically).
     *
     * @return type the url associated with the submission submitted later or null if none found.
     */
    private function get_later_submission_url()
    {
        $later_submission_information = $this->get_later_submission_information(
            $this->get_submitter_type(),
            $this->get_target_id(),
            $this->get_submission_id()
        );
        if ($later_submission_information)
        {
            return $this->get_url(array(self::PARAM_SUBMISSION => $later_submission_information));
        }

        return null;
    }

    /**
     * Generates the HTML associated with the navigation bar for the submissions of the current submitter.
     *
     * @return type the HTML associated with the submissions navigation bar.
     */
    private function generate_submissions_navigator()
    {
        $html = array();

        $html[] = '<div style="text-align:center;">';
        $earlier_submission_url = $this->get_earlier_submission_url();
        if ($earlier_submission_url)
        {
            $html[] = '<a href="' . $earlier_submission_url . '">';
            $html[] = '<img src="' . Theme::getInstance()->getCommonImagePath('Action/Prev') . '"/>';
            $html[] = Translation::get('EarlierSubmission');
            $html[] = '</a>';
        }
        else
        {
            $html[] = '<img src="' . Theme::getInstance()->getCommonImagePath('Action/PrevNa') . '"/>';
            $html[] = Translation::get('EarlierSubmission');
        }
        $submissionPosition = $this->get_position_submissions($this->get_submission_id());
        $submissionsCount = $this->get_count_submissions();

        $html[] = ' [' . $submissionPosition . '/' . $submissionsCount . '] ';

        $later_submission_url = $this->get_later_submission_url();
        if ($later_submission_url)
        {
            $html[] = '<a href="' . $later_submission_url . '">';
            $html[] = Translation::get('LaterSubmission');
            $html[] = '<img src="' . Theme::getInstance()->getCommonImagePath('Action/Next') . '"/>';
            $html[] = '</a>';
        }
        else
        {
            $html[] = Translation::get('LaterSubmission');
            $html[] = '<img src="' . Theme::getInstance()->getCommonImagePath('Action/NextNa') . '"/>';
        }
        $html[] = '</div>';

        $html = array();
//        $html[] = '<div style="float: left;">';
//        $html[] = '<div style="border-radius: 5px; padding: 7px 15px; margin: 10px 10px 10px 0; background-color: #23c6c8; color: white;">';
//        $html[] = '<div class="row">';
//        $html[] = '<div class="col-xs-3">';
//        $html[] = '<i class="fa fa-file-text-o fa-2x" style="margin-top: 7px; margin-right: 10px;"></i>';
//        $html[] = '</div>';
//        $html[] = '<div class="col-xs-9 text-right" style="font-size: 2em;">';
//        $html[] = $submissionPosition . '/' . $submissionsCount;
//        $html[] = '</div>';
//        $html[] = '</div>';
//        $html[] = '</div>';
//        $html[] = '</div>';

        $html[] = '<div class="col-lg-6 col-sm-4">';
        $html[] = '<div class="panel panel-default">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<div class="pull-left">';
        $html[] = '<h5 class="panel-title">';
        $html[] = '<i class="fa fa-file-text-o" style="font-size: 16px; margin-top: 2px; margin-right: 3px;"></i>';
        $html[] = Translation::get('Submissions');
        $html[] = '</h5>';
        $html[] = '</div>';
        $html[] = '<div class="pull-right">';
        $html[] = '<span class="badge" style="color: white; background-color: #5bc0de;">';
        $html[] = $submissionPosition . ' / ' . $submissionsCount;
        $html[] = '</span>';
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';

        $html[] = '<div class="btn-group">';
        $html[] = '<button class="btn btn-default"><i class="fa fa-backward" style="font-size: 16px; margin-right: 10px;"></i>' . Translation::get('EarlierSubmission') . '</button>';
        $html[] = '<div class="btn btn-default"><span class="badge" style="color: white; background-color: #28a745;">' . $submissionPosition . ' / ' . $submissionsCount . '</span></div>';
        $html[] = '<button class="btn btn-default"><i class="fa fa-forward" style="font-size: 16px; margin-right: 10px;"></i>' . Translation::get('LaterSubmission') . '</button>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Creates the navigation bar for navigating between submitters.
     *
     * @return type the HTML associated with the submitters navigation bar.
     */
    private function generate_submitters_navigator()
    {
        $html = array();
        $html[] = '<div style="text-align:center;">';
        $previous_submitter_url = $this->get_previous_submitter_url();
        if ($previous_submitter_url)
        {
            $html[] = '<a href="' . $previous_submitter_url . '">';
            $html[] = '<img src="' . Theme::getInstance()->getCommonImagePath('Action/Prev') . '"/>';
            $html[] = Translation::get('PreviousSubmitter');
            $html[] = '</a>';
        }
        else
        {
            $html[] = '<img src="' . Theme::getInstance()->getCommonImagePath('Action/PrevNa') . '"/>';
            $html[] = Translation::get('PreviousSubmitter');
        }
        $submitterPosition =
            $this->get_position_submitter_with_submissions($this->get_submitter_type(), $this->get_target_id());

        $submittersCount = $this->get_count_submitters($this->get_submitter_type());

        $html[] = ' [' . $submitterPosition . '/' . $submittersCount . '] ';
        $next_submitter_url = $this->get_next_submitter_url();
        if ($next_submitter_url)
        {
            $html[] = '<a href="' . $next_submitter_url . '">';
            $html[] = Translation::get('NextSubmitter');
            $html[] = '<img src="' . Theme::getInstance()->getCommonImagePath('Action/Next') . '"/>';
            $html[] = '</a>';
        }
        else
        {
            $html[] = Translation::get('NextSubmitter');
            $html[] = '<img src="' . Theme::getInstance()->getCommonImagePath('Action/NextNa') . '"/>';
        }
        $html[] = '</div>';

        $html = array();

        $html[] = '<div class="col-lg-6 col-sm-4">';
        $html[] = '<div class="panel panel-default">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<div class="pull-left">';
        $html[] = '<h5 class="panel-title">';
        $html[] = '<i class="fa fa-user" style="font-size: 16px; margin-top: 2px; margin-right: 3px;"></i>';
        $html[] = Translation::get('Submitters');
        $html[] = '</h5>';
        $html[] = '</div>';
        $html[] = '<div class="pull-right">';
        $html[] = '<span class="badge" style="color: white; background-color: #28a745;">';
        $html[] = $submitterPosition . ' / ' . $submittersCount;
        $html[] = '</span>';
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';

        $html[] = '<div class="btn-group">';
        $html[] = '<button class="btn btn-default"><i class="fa fa-backward" style="font-size: 16px; margin-right: 10px;"></i>' . Translation::get('PreviousSubmitter') . '</button>';
        $html[] = '<div class="btn btn-default"><span class="badge" style="color: white; background-color: #28a745;">' . $submitterPosition . ' / ' . $submittersCount . '</span></div>';
        $html[] = '<button class="btn btn-default"><i class="fa fa-forward" style="font-size: 16px; margin-right: 10px;"></i>' . Translation::get('NextSubmitter') . '</button>';
        $html[] = '</div>';

//        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';


//        $html[] = '<div style="float: left;">';
//        $html[] = '<div style="border-radius: 5px; padding: 7px 15px; margin: 10px 10px 10px 0; background-color: #1ab394; color: white;">';
//        $html[] = '<div class="row">';
//        $html[] = '<div class="col-xs-3">';
//        $html[] = '<i class="fa fa-user fa-2x" style="margin-top: 7px; margin-right: 5px;"></i>';
//        $html[] = '</div>';
//        $html[] = '<div class="col-xs-9 text-right" style="font-size: 2em;">';
//        $html[] = $submitterPosition . '/' . $submittersCount;
//        $html[] = '</div>';
//        $html[] = '</div>';
//        $html[] = '</div>';
//        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Gets the url for the previous submitter with submissions.
     *
     * @return type the url of the previous submitter's latest submission or null if none found.
     */
    private function get_previous_submitter_url()
    {
        $previous_submitter_information = $this->get_previous_submitter_information(
            $this->get_submitter_type(),
            $this->get_target_id()
        );
        if (!$previous_submitter_information)
        {
            return null;
        }
        while (!$previous_submitter_information[AssignmentSubmission::PROPERTY_ID])
        {
            $previous_submitter_information = $this->get_previous_submitter_information(
                $previous_submitter_information[AssignmentSubmission::PROPERTY_SUBMITTER_TYPE],
                $previous_submitter_information[AssignmentSubmission::PROPERTY_SUBMITTER_ID]
            );
            if (!$previous_submitter_information)
            {
                return null;
            }
        }

        return $this->get_url(
            array(
                self::PARAM_TARGET_ID => $previous_submitter_information[AssignmentSubmission::PROPERTY_SUBMITTER_ID],
                self::PARAM_SUBMITTER_TYPE => $previous_submitter_information[AssignmentSubmission::PROPERTY_SUBMITTER_TYPE],
                self::PARAM_SUBMISSION => $previous_submitter_information[AssignmentSubmission::PROPERTY_ID]
            )
        );
    }

    /**
     * Gets the url for the next submitter with submissions.
     *
     * @return type the url of the next submitter's latest submission or null if none found.
     */
    private function get_next_submitter_url()
    {
        $next_submitter_information = $this->get_next_submitter_information(
            $this->get_submitter_type(),
            $this->get_target_id()
        );
        if (!$next_submitter_information)
        {
            return null;
        }
        while (!$next_submitter_information[AssignmentSubmission::PROPERTY_ID])
        {
            $next_submitter_information = $this->get_next_submitter_information(
                $next_submitter_information[AssignmentSubmission::PROPERTY_SUBMITTER_TYPE],
                $next_submitter_information[AssignmentSubmission::PROPERTY_SUBMITTER_ID]
            );
            if (!$next_submitter_information)
            {
                return null;
            }
        }

        return $this->get_url(
            array(
                self::PARAM_TARGET_ID => $next_submitter_information[AssignmentSubmission::PROPERTY_SUBMITTER_ID],
                self::PARAM_SUBMITTER_TYPE => $next_submitter_information[AssignmentSubmission::PROPERTY_SUBMITTER_TYPE],
                self::PARAM_SUBMISSION => $next_submitter_information[AssignmentSubmission::PROPERTY_ID]
            )
        );
    }

    /**
     * Returns the content of the score section.
     *
     * @return string
     */
    private function get_score_section_content()
    {
        $html = array();

        // Course admin can edit score
        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            // Show the score form
            $html[] = $this->score_section->toHtml();
        }
        // Students can only see their score and not edit
        else
        {
            $html[] = '<div style="font-weight:bold;float:left;">';
            $html[] = Translation::get('Score') . ':&nbsp;<br />';
            $html[] = '</div>';

            $score_tracker = $this->get_score_tracker();
            if ($score_tracker)
            {
                $html[] = $this->get_score_tracker()->get_score() . '%<br />';
            }
            else
            {
                $html[] = '-<br />';
            }
            $html[] = '<div style="float:left;">';
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the content of the feedback section.
     *
     * @return string
     */
    private function get_feedback_section_content()
    {
        $html = array();

        // Show the feedbacks
        $html[] = $this->get_feedbacks_html();

        // Show the feedback form (only for course admins)
        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $html[] = $this->feedback_section->toHtml();
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the feedbacks with their creation date as html code.
     *
     * @return string
     */
    private function get_feedbacks_html()
    {
        $html = array();

        $feedback_trackers = $this->get_feedback_trackers();
        if (count($feedback_trackers) == 0 && !$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $html[] = Translation::get('NoFeedbacks');
        }
        else
        {
            while ($feedback_tracker = $feedback_trackers->next_result())
            {
                $html[] = $this->get_feedback_html($feedback_tracker);
            }
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the display as html code of the given feedback tracker.
     *
     * @param $feedback_tracker SubmissionFeedback
     *
     * @return string
     */
    private function get_feedback_html($feedback_tracker)
    {
        $submission_tracker = $this->get_submission_tracker();
        $feedback = $feedback_tracker->get_content_object();

        $html = array();

        if ($feedback)
        {
            $html[] = '<div class="panel panel-default">';
            $html[] = '<div class="panel-body">';

            // Show the content object
            $html[] = '<div>';
            $display = ContentObjectRenditionImplementation::factory(
                $feedback,
                ContentObjectRendition::FORMAT_HTML,
                ContentObjectRendition::VIEW_FULL,
                $this
            );
            $html[] = $display->render();
            $html[] = '</div>';

            // Publication info
            $html[] = '<div class="publication_info" style="float: left;">';
            $html[] = Translation::get('CreatedOn');
            $html[] = $this->format_date($feedback_tracker->get_created());
            $html[] = Translation::get('By', null, Utilities::COMMON_LIBRARIES);
            $html[] = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User::class_name(),
                $feedback_tracker->get_user_id()
            )->get_fullname();
            $html[] = '</div>';

            // Toolbar (only for course admins)
            if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
            {
                $toolbar = new Toolbar();
                // Edit button
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('EditFeedback'),
                        Theme::getInstance()->getCommonImagePath('Action/Edit'),
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_EDIT_FEEDBACK,
                                self::PARAM_FEEDBACK_ID => $feedback_tracker->get_id(),
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $submission_tracker->get_publication_id(
                                ),
                                self::PARAM_SUBMISSION => $submission_tracker->get_id()
                            )
                        ),
                        ToolbarItem::DISPLAY_ICON
                    )
                );
                // Remove button
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('DeleteFeedback'),
                        Theme::getInstance()->getCommonImagePath('Action/Delete'),
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DELETE_FEEDBACK,
                                self::PARAM_FEEDBACK_ID => $feedback_tracker->get_id(),
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $submission_tracker->get_publication_id(
                                )
                            )
                        ),
                        ToolbarItem::DISPLAY_ICON,
                        true
                    )
                );

                $html[] = '<div class="submission_toolbar">' . $toolbar->as_html() . '</div>';
            }

            $html[] = '</div></div><div class="clear">&nbsp;</div><br />';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the content of the notes section.
     *
     * @return string
     */
    private function get_notes_section_content()
    {
        $html = array();

        $html[] = $this->notes_section->toHtml();

        return implode(PHP_EOL, $html);
    }

    /**
     * Displays the title of a section and the content.
     *
     * @param $title string The title for the section
     * @param $content array The content as an array of string in html code
     */
    private function display_section($title, $content)
    {
        $html = array();

        $html[] = $this->set_section_content($title, $content);

        return implode(PHP_EOL, $html);
    }

    /**
     * Sets the title and content of a section.
     *
     * @param $title string The title for the section
     * @param string $html_content
     *
     * @return array The content
     */
    function set_section_content($title, $html_content)
    {
        $html = array();

        $html[] = '<div class="panel panel-default">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<h5 class="panel-title">';
        $html[] = Translation::get($title);
        $html[] = '</h5>';
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';

        // Content
        $html[] = $html_content;

        // Closes the section
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns a formatted string of the given date.
     *
     * @param $date type
     *
     * @return string The formatted date
     */
    private function format_date($date)
    {
        $formatted_date = DatetimeUtilities::format_locale_date(
            Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES),
            $date
        );

        return $formatted_date;
    }

    /**
     * Displays the header.
     */
    function render_header()
    {
        $html = array();

        $html[] = parent::render_header();

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        if ($this->buttonToolbarRenderer)
        {
            $html[] = $this->buttonToolbarRenderer->render();
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the action bar with actions.
     *
     * @return ButtonToolBarRenderer The action bar
     */
    private function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            $submittersNavigatorActions = new ButtonGroup();
            $submissionsNavigatorActions = new ButtonGroup();

            if ($this->submission)
            {
                $commonActions->addButton(
                    new Button(
                        Translation::get('ViewSubmission'),
                        new FontAwesomeGlyph('file-text-o'),
                        'javascript:openPopup(\'' . $this->generate_attachment_viewer_url(
                            $this->submission,
                            AttachmentViewerComponent::TYPE_SUBMISSION
                        ) . '\');void(0);',
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
            {
                if (self::is_downloadable($this->submission))
                {
                    $commonActions->addButton(
                        new Button(
                            Translation::get('DownloadSubmission'),
                            new FontAwesomeGlyph('download'),
                            $this->get_url(
                                array(
                                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DOWNLOAD_SUBMISSIONS,
                                    self::PARAM_SUBMISSION => $this->get_submission_tracker()->get_id()
                                )
                            ),
                            ToolbarItem::DISPLAY_ICON_AND_LABEL
                        )
                    );
                }

                $commonActions->addButton(
                    new Button(
                        Translation::get('DeleteSubmission'),
                        new FontAwesomeGlyph('remove'),
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DELETE_SUBMISSION,
                                self::PARAM_SUBMISSION => $this->get_submission_tracker()->get_id()
                            )
                        ),
                        ToolbarItem::DISPLAY_ICON_AND_LABEL,
                        true
                    )
                );

                $commonActions->addButton(
                    new Button(
                        Translation::get('AddFeedback'),
                        new FontAwesomeGlyph('commenting-o'),
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_GIVE_FEEDBACK,
                                self::PARAM_SUBMISSION => $this->get_submission_tracker()->get_id(),
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->get_publication_id(
                                ),
                                self::PARAM_TARGET_ID => $this->get_target_id(),
                                self::PARAM_SUBMITTER_TYPE => $this->get_submitter_type()
                            )
                        ),
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );

                $earlier_submission_url = $this->get_earlier_submission_url();
                $later_submission_url = $this->get_later_submission_url();
                $submissionPosition = $this->get_position_submissions($this->get_submission_id());
                $submissionsCount = $this->get_count_submissions();

                $submissionsNavigatorActions->addButton(
                    new Button(
                        Translation::get('EarlierSubmission'),
                        new FontAwesomeGlyph('backward'),
                        $earlier_submission_url,
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );

                $submissionsNavigatorActions->addButton(
                    new Button(
                        '<span class="badge" style="color: white; background-color: #28a745;">' . $submissionPosition . ' / ' . $submissionsCount . '</span>',
                        null,
                        '#',
                        ToolbarItem::DISPLAY_LABEL
                    )
                );

                $submissionsNavigatorActions->addButton(
                    new Button(
                        Translation::get('LaterSubmission'),
                        new FontAwesomeGlyph('forward'),
                        $later_submission_url,
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );

                $previous_submitter_url = $this->get_previous_submitter_url();
                $next_submitter_url = $this->get_next_submitter_url();
                $submitterPosition =
                    $this->get_position_submitter_with_submissions($this->get_submitter_type(), $this->get_target_id());

                $submittersCount = $this->get_count_submitters($this->get_submitter_type());

                $submittersNavigatorActions->addButton(
                    new Button(
                        Translation::get('PreviousSubmitter'),
                        new FontAwesomeGlyph('backward'),
                        $previous_submitter_url,
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );

                $submittersNavigatorActions->addButton(
                    new Button(
                        '<span class="badge" style="color: white; background-color: #5bc0de;">' . $submitterPosition . ' / ' . $submittersCount . '</span>',
                        null,
                        '#',
                        ToolbarItem::DISPLAY_LABEL
                    )
                );

                $submittersNavigatorActions->addButton(
                    new Button(
                        Translation::get('NextSubmitter'),
                        new FontAwesomeGlyph('forward'),
                        $next_submitter_url,
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($submittersNavigatorActions);
            $buttonToolbar->addButtonGroup($submissionsNavigatorActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     * Adds additional breadcrumbs.
     *
     * @param $breadcrumbtrail BreadcrumbTrail The current trail
     */
    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->find_data();

        $breadcrumbtrail->add(
            new Breadcrumb($this->define_browse_submitters_action_url(), $this->get_assignment()->get_title())
        );

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_BROWSE_SUBMISSIONS,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->get_publication_id(),
                        self::PARAM_TARGET_ID => $this->get_target_id(),
                        self::PARAM_SUBMITTER_TYPE => $this->get_submitter_type()
                    )
                ),
                $this->get_submitter_name()
            )
        );
    }

    /**
     * Returns an url for the submitters browser based on who clicked on the link.
     *
     * @return string The url
     */
    private function define_browse_submitters_action_url()
    {
        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            return $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_BROWSE_SUBMITTERS,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->get_publication_id()
                )
            );
        }

        return $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_STUDENT_BROWSE_SUBMISSIONS,
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->get_publication_id()
            )
        );
    }

    /**
     * Changes the last breadcrumb to the name of the submission.
     */
    private function change_last_breadcrumb()
    {
        $breadcrumb_trail = BreadcrumbTrail::getInstance();
        $breadcrumbs = $breadcrumb_trail->get_breadcrumbs();

        $title = $this->submission ? $this->submission->get_title() : Translation::get('UnknownContentObject');

        $breadcrumbs[$breadcrumb_trail->size() - 1] = new Breadcrumb(
            $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW_SUBMISSION,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->get_publication_id(),
                    self::PARAM_TARGET_ID => $this->get_target_id(),
                    self::PARAM_SUBMITTER_TYPE => $this->get_submitter_type()
                )
            ),
            $title . ' - ' . Translation::get('Detail')
        );

        $breadcrumb_trail->set_breadcrumbtrail($breadcrumbs);
    }
}

?>
