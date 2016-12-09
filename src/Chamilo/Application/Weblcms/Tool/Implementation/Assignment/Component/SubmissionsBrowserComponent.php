<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmitterSubmissions\SubmitterGroupSubmissionsTable;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmitterSubmissions\SubmitterUserSubmissionsTable;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager as CourseGroupDataManager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.weblcms.tool.assignment.php.component Viewer for the submissions
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Anthony Hurst (Hogeschool Gent)
 * @author Bert De Clercq (Hogeschool Gent)
 */
class SubmissionsBrowserComponent extends SubmissionsManager implements TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * The assignment content object
     * 
     * @var Assignment
     */
    private $assignment;

    /**
     * The id of the assignment publication
     * 
     * @var int
     */
    private $publication_id;

    /**
     * ID of the submitter (user or group)
     * 
     * @var int
     */
    private $submitter_id;

    /**
     * The type of the submitter (user = 0, course group = 1, platform group = 2)
     * 
     * @var int
     */
    private $submitter_type;

    /**
     * Submissions of the submitter for an assignment
     * 
     * @var array \application\weblcms\integration\core\tracking\tracker\AssignmentSubmission
     */
    private $submissions;

    public function run()
    {
        $this->test_view_rights();
        $this->submitter_id = Request :: get(self :: PARAM_TARGET_ID);
        $this->submitter_type = Request :: get(self :: PARAM_SUBMITTER_TYPE);
        
        $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID, $this->publication_id);
        $this->set_parameter(self :: PARAM_TARGET_ID, $this->submitter_id);
        $this->set_parameter(self :: PARAM_SUBMITTER_TYPE, $this->submitter_type);
        
        switch ($this->submitter_type)
        {
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_USER :
                $breadcrumb_title = \Chamilo\Core\User\Storage\DataManager :: get_fullname_from_user(
                    $this->submitter_id);
                break;
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_COURSE_GROUP :
                $breadcrumb_title = \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager :: retrieve_by_id(
                    CourseGroup :: class_name(), 
                    $this->submitter_id)->get_name();
                break;
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_PLATFORM_GROUP :
                $breadcrumb_title = \Chamilo\Core\Group\Storage\DataManager :: retrieve_by_id(
                    Group :: class_name(), 
                    $this->submitter_id)->get_name();
                break;
        }
        
        $breadcrumb_trail = BreadcrumbTrail :: get_instance();
        $breadcrumbs = $breadcrumb_trail->get_breadcrumbs();
        $breadcrumbs[$breadcrumb_trail->size() - 1] = new Breadcrumb(
            $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_BROWSE_SUBMISSIONS, 
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $this->publication_id, 
                    self :: PARAM_TARGET_ID => $this->submitter_id, 
                    self :: PARAM_SUBMITTER_TYPE => $this->submitter_type)), 
            $breadcrumb_title);
        $breadcrumb_trail->set_breadcrumbtrail($breadcrumbs);
        
        $this->get_submissions_from_target();
        return $this->display_assignment_submissions();
    }

    /**
     * This function checks whether or not a user has permission to access this page.
     */
    private function test_view_rights()
    {
        $publication = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(), 
            $this->get_publication_id());
        
        if (! $this->is_allowed(WeblcmsRights :: VIEW_RIGHT, $publication) ||
             ! $this->is_allowed(WeblcmsRights :: VIEW_RIGHT))
        {
            $this->redirect(
                Translation :: get("NotAllowed", null, Utilities :: COMMON_LIBRARIES), 
                true, 
                array(), 
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION, 
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID));
        }
        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT) || $this->assignment->get_visibility_submissions())
        {
            return;
        }
        $is_member = false;
        switch ($this->get_submitter_type())
        {
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_COURSE_GROUP :
                $is_member = $this->is_course_group_member($this->get_target_id(), $this->get_user_id());
                break;
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_PLATFORM_GROUP :
                $is_member = $this->is_platform_group_member($this->get_target_id(), $this->get_user_id());
                break;
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_USER :
                $is_member = $this->is_current_user($this->get_target_id());
                break;
        }
        if (! $is_member)
        {
            $this->redirect(
                Translation :: get("NotAllowed", null, Utilities :: COMMON_LIBRARIES), 
                true, 
                array(), 
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION, 
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID));
        }
    }

    private function is_course_group_member($group_id, $user_id)
    {
        return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager :: is_course_group_member(
            $group_id, 
            $user_id);
    }

    private function is_platform_group_member($group_id, $user_id)
    {
        $group = \Chamilo\Core\Group\Storage\DataManager :: retrieve_by_id(Group :: class_name(), $group_id);
        if (\Chamilo\Core\Group\Storage\DataManager :: is_group_member($group_id, $user_id))
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

    private function is_current_user($user_id)
    {
        return $this->get_user_id() == $user_id;
    }

    public function get_assignment()
    {
        return $this->assignment;
    }

    /**
     * Gets all submissions from a target user or group for the publication
     */
    private function get_submissions_from_target()
    {
        $order_by[] = new OrderBy(
            new PropertyConditionVariable(
                AssignmentSubmission :: class_name(), 
                AssignmentSubmission :: PROPERTY_DATE_SUBMITTED));
        $this->submissions = DataManager :: retrieves(
            AssignmentSubmission :: class_name(), 
            new DataClassRetrievesParameters($this->get_table_conditions(), null, null, $order_by));
    }

    /**
     * Constructs the conditions needed by the database manager to retrieve the necessary submissions.
     * 
     * @return \libraries\storage\AndCondition the aggregate of all the conditions applicable.
     */
    private function get_table_conditions()
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: PROPERTY_PUBLICATION_ID), 
            new StaticConditionVariable($this->publication_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: PROPERTY_SUBMITTER_ID), 
            new StaticConditionVariable($this->submitter_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: PROPERTY_SUBMITTER_TYPE), 
            new StaticConditionVariable($this->submitter_type));
        
        return new AndCondition($conditions);
    }

    /**
     * Displays the assignment, submitter and submissions
     * 
     * @return null
     */
    // FIXME too much different stuff for this function
    private function display_assignment_submissions()
    {
        $is_user = $this->submitter_type ==
             \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_USER;
        $is_course_group = $this->submitter_type ==
             \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_COURSE_GROUP;
        
        if ($is_user)
        {
        }
        elseif ($is_course_group)
        {
            $group = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
                CourseGroup :: class_name(), 
                $this->submitter_id);
        }
        else
        {
            $group = \Chamilo\Core\Group\Storage\DataManager :: retrieve_by_id(
                Group :: class_name(), 
                $this->submitter_id);
        }
        
        $is_user = $this->submitter_type ==
             \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_USER;
        
        // Check if toolbar should be shown
        if ($is_user && $this->get_user_id() == $this->submitter_id)
        {
            $display_add = true;
        }
        elseif (! $is_user)
        {
            $is_course_group = $this->submitter_type ==
                 \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_COURSE_GROUP;
            
            if ($is_course_group)
            {
                $display_add = \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager :: is_course_group_member(
                    $group->get_id(), 
                    $this->get_user_id());
            }
            else
            {
                $display_add = $this->is_group_member($group);
            }
        }
        
        $html = array();
        
        $html[] = $this->render_header($display_add);
        $html[] = $this->generate_navigation_bar_html();
        $html[] = '<div class="announcements level_1" style="background-image: url(' .
             Theme :: getInstance()->getCommonImagePath('ContentObject/Introduction') . ')">';
        $html[] = $this->generate_assignment_details_html();
        
        // Display group members
        if ($this->submitter_type !=
             \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_USER)
        {
            $html[] = '<div class="description" style="font-weight:bold;float:left">';
            $html[] = Translation :: get('GroupMembers') . ':&nbsp;';
            $html[] = '</div>';
            $html[] = '<div style="float:left">';
            $html[] = $this->display_group_members();
            $html[] = '<br/><br/></div>';
        }
        $html[] = $this->get_reporting_as_html();
        $html[] = '</div>';
        
        if ($this->submitter_type ==
             \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_USER)
        {
            $table = new SubmitterUserSubmissionsTable($this);
            $html[] = $table->as_html();
        }
        else
        {
            $table = new SubmitterGroupSubmissionsTable($this);
            $html[] = $table->as_html();
        }
        
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    private function generate_navigation_bar_html()
    {
        $html = array();
        $html[] = '<div class="announcements level_2" style="background-image:url(' .
             Theme :: getInstance()->getCommonImagePath('ContentObject/Introduction') . ';width=100%">';
        
        if ($this->assignment->get_visibility_submissions() || $this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $html[] = $this->generate_submitters_navigator();
        }
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div><br/>';
        return implode(PHP_EOL, $html);
    }

    private function generate_submitters_navigator()
    {
        $html = array();
        $html[] = '<div style="text-align:center">';
        $previous_submitter_url = $this->get_previous_submitter_url();
        if ($previous_submitter_url)
        {
            $html[] = '<a href="' . $previous_submitter_url . '">';
            $html[] = '<img src="' . Theme :: getInstance()->getCommonImagePath('Action/Prev') . '"/>';
            $html[] = Translation :: get('PreviousSubmitter');
            $html[] = '</a>';
        }
        else
        {
            $html[] = '<img src="' . Theme :: getInstance()->getCommonImagePath('Action/PrevNa') . '"/>';
            $html[] = Translation :: get('PreviousSubmitter');
        }
        $html[] = ' [' . $this->get_position_submitter($this->get_submitter_type(), $this->get_target_id()) . '/' .
             $this->get_count_submitters($this->get_submitter_type()) . '] ';
        $next_submitter_url = $this->get_next_submitter_url();
        if ($next_submitter_url)
        {
            $html[] = '<a href="' . $next_submitter_url . '">';
            $html[] = Translation :: get('NextSubmitter');
            $html[] = '<img src="' . Theme :: getInstance()->getCommonImagePath('Action/Next') . '"/>';
            $html[] = '</a>';
        }
        else
        {
            $html[] = Translation :: get('NextSubmitter');
            $html[] = '<img src="' . Theme :: getInstance()->getCommonImagePath('Action/NextNa') . '"/>';
        }
        $html[] = '</div>';
        return implode(PHP_EOL, $html);
    }

    private function get_previous_submitter_url()
    {
        $previous_submitter_information = $this->get_previous_submitter_information(
            $this->get_submitter_type(), 
            $this->get_target_id());
        if (! $previous_submitter_information)
        {
            return null;
        }
        return $this->get_url(
            array(
                self :: PARAM_TARGET_ID => $previous_submitter_information[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: PROPERTY_SUBMITTER_ID], 
                self :: PARAM_SUBMITTER_TYPE => $previous_submitter_information[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: PROPERTY_SUBMITTER_TYPE], 
                self :: PARAM_SUBMISSION => $previous_submitter_information[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: PROPERTY_ID]));
    }

    private function get_next_submitter_url()
    {
        $next_submitter_information = $this->get_next_submitter_information(
            $this->get_submitter_type(), 
            $this->get_target_id());
        if (! $next_submitter_information)
        {
            return null;
        }
        return $this->get_url(
            array(
                self :: PARAM_TARGET_ID => $next_submitter_information[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: PROPERTY_SUBMITTER_ID], 
                self :: PARAM_SUBMITTER_TYPE => $next_submitter_information[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: PROPERTY_SUBMITTER_TYPE], 
                self :: PARAM_SUBMISSION => $next_submitter_information[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: PROPERTY_ID]));
    }

    /**
     * Recursively iterates over a platform group and its subgroups to identify whether the current user is a member of
     * the platform group at any level.
     * 
     * @param Group $group the platform group.
     * @return boolean whether the user is a member of the platform group
     */
    private function is_group_member($group)
    {
        // DMTODO The GroupDataManager should provide a simpler way to find out
        if (\Chamilo\Core\Group\Storage\DataManager :: is_group_member($group->get_id(), $this->get_user_id()))
        {
            return true;
        }
        if ($group->has_children())
        {
            foreach ($group->get_subgroups() as $subgroup)
            {
                if ($this->is_group_member($subgroup))
                {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Retrieves the members of a group.
     * 
     * @return String a comma separated list of names.
     */
    private function display_group_members()
    {
        if ($this->submitter_type ==
             \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_COURSE_GROUP)
        {
            $users = CourseGroupDataManager :: retrieve_course_group_users($this->submitter_id)->as_array();
        }
        else
        {
            $users = \Chamilo\Core\User\Storage\DataManager :: retrieves(
                User :: class_name(), 
                new DataClassRetrievesParameters(
                    new InCondition(
                        new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID), 
                        \Chamilo\Core\Group\Storage\DataManager :: retrieve_by_id(
                            Group :: class_name(), 
                            $this->submitter_id)->get_users(true, true))))->as_array();
        }
        
        $user_names = array();
        
        foreach ($users as $user)
        {
            $user_names[$user->get_lastname()] = $user->get_fullname();
        }
        
        ksort($user_names);
        
        return implode(", ", $user_names);
    }

    public function get_search_condition()
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        
        if (isset($query) && $query != '')
        {
            return new PatternMatchCondition(
                new PropertyConditionVariable(self :: class_name(), self :: PROPERTY_TITLE), 
                '*' . $query . '*');
        }
        
        return null;
    }

    /**
     * Returns the reporting as an array of strings.
     * 
     * @return array The reporting
     */
    private function get_reporting_as_html()
    {
        $title = array();
        if ($this->is_feedback_visible($this->assignment, $this->has_submissions()))
        {
            $title[] = Translation :: get('AutomaticFeedback');
        }
        $title[] = Translation :: get('Reporting');
        
        $html = array();
        
        $html[] = '<div class="title" style="border-top:1px dotted #D3D3D3;padding-top:5px;width:100%;">';
        $html[] = implode(' & ', $title);
        $html[] = '</div><div class="clear">&nbsp;</div>';
        
        if ($this->is_feedback_visible($this->assignment, $this->has_submissions()))
        {
            $html[] = $this->generate_automatic_feedback_html();
        }
        
        if (! $this->is_feedback_visible($this->assignment, $this->has_submissions()))
        {
            $html[] = '<br />';
        }
        
        $html[] = '<div style="font-weight:bold;float:left;">';
        $html[] = Translation :: get('SubmissionsWithScore') . ':&nbsp;<br />';
        $html[] = Translation :: get('SubmissionsWithFeedback') . ':&nbsp;<br/>';
        $html[] = Translation :: get('AverageScore') . ':&nbsp;<br />';
        $html[] = '</div>';
        
        $html[] = '<div style="float:left;">';
        $html[] = $this->get_reporting_data_as_html();
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Checks whether the table has submissions.
     * 
     * @return boolean True if the table has submissions
     */
    private function has_submissions()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: count_data(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: class_name(), 
            null, 
            $this->get_table_conditions()) > 0;
    }

    /**
     * Returns the reporting data as an array of strings.
     * 
     * @return array The reporting data
     */
    private function get_reporting_data_as_html()
    {
        $score = 0;
        $count_score = 0;
        $count_feedback = 0;
        $count_submissions = 0;
        
        foreach ($this->submissions->as_array() as $submission)
        {
            $score_tracker = $this->get_score_tracker_for_submission($submission->get_id());
            
            if ($score_tracker)
            {
                $score += $score_tracker->get_score();
                $count_score ++;
            }
            
            $feedback_tracker = $this->get_feedback_tracker_for_submission($submission->get_id());
            
            if ($feedback_tracker)
            {
                $count_feedback ++;
            }
            
            $count_submissions ++;
        }
        
        $html = array();
        $html[] = $count_score . '/' . $count_submissions . '<br />';
        $html[] = $count_feedback . '/' . $count_submissions . '<br/>';
        
        if ($count_score == 0)
        {
            $html[] = '-';
        }
        else
        {
            $html[] = round($score / $count_score, 2) . '%<br />';
        }
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Gets a toolbar with common actions
     * 
     * @return ButtonToolBarRenderer
     */
    public function getButtonToolbarRenderer($display_add)
    {
        // $display_add = false;
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            
            if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
            {
                if ($this->get_submissions_tracker($this->get_submitter_type(), $this->get_target_id()))
                {
                    $commonActions->addButton(
                        new Button(
                            Translation :: get('DownloadAllSubmissions'), 
                            Theme :: getInstance()->getCommonImagePath('Action/Download'), 
                            $this->get_url(
                                array(
                                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_DOWNLOAD_SUBMISSIONS)), 
                            ToolbarItem :: DISPLAY_ICON_AND_LABEL));
                }
                
                $commonActions->addButton(
                    new Button(
                        Translation :: get('ScoresOverview'), 
                        Theme :: getInstance()->getCommonImagePath('Action/Statistics'), 
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Manager :: PARAM_TOOL => \Chamilo\Application\Weblcms\Manager :: ACTION_REPORTING, 
                                \Chamilo\Application\Weblcms\Manager :: PARAM_TEMPLATE_ID => \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\CourseSubmitterSubmissionsTemplate :: class_name(), 
                                \Chamilo\Application\Weblcms\Manager :: PARAM_PUBLICATION => $this->get_publication_id(), 
                                self :: PARAM_TARGET_ID => $this->get_target_id(), 
                                self :: PARAM_SUBMITTER_TYPE => $this->get_submitter_type(), 
                                \Chamilo\Application\Weblcms\Manager :: PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager :: ACTION_VIEW)), 
                        ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
            
            if ($display_add)
            {
                $commonActions->addButton(
                    new Button(
                        Translation :: get('SubmissionSubmit'), 
                        Theme :: getInstance()->getCommonImagePath('Action/Add'), 
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_SUBMIT_SUBMISSION, 
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $this->get_publication_id())), 
                        ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
            $buttonToolbar->addButtonGroup($commonActions);
            
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }
        
        return $this->buttonToolbarRenderer;
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->publication_id = Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
        
        $pub = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(), 
            $this->get_publication_id());
        
        if (! $this->is_allowed(WeblcmsRights :: VIEW_RIGHT, $pub))
        {
            $this->redirect(
                Translation :: get("NotAllowed", null, Utilities :: COMMON_LIBRARIES), 
                true, 
                array(), 
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION, 
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID, 
                    self :: PARAM_TARGET_ID, 
                    self :: PARAM_SUBMITTER_TYPE));
        }
        
        $this->assignment = $pub->get_content_object();
    }

    public function render_header($display_add)
    {
        $html = array();
        
        $html[] = parent :: render_header();
        
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer($display_add);
        
        if ($this->buttonToolbarRenderer)
        {
            $html[] = $this->buttonToolbarRenderer->render();
        }
        
        return implode(PHP_EOL, $html);
    }

    public function get_table_condition($table_class_name)
    {
        $conditions = array();
        
        $searchConditions = $this->get_search_condition();
        
        if ($searchConditions)
        {
            $conditions[] = $searchConditions;
        }
        
        $conditions[] = $this->get_table_conditions();
        return new AndCondition($conditions);
    }
}
