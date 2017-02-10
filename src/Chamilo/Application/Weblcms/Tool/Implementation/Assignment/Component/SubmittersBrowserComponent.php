<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionCourseGroupBrowser\SubmissionCourseGroupsBrowserTable;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionGroupsBrowser\SubmissionGroupsBrowserTable;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionUsersBrowser\SubmissionUsersBrowserTable;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.weblcms.tool.assignment.php.component Browser of the users and groups the assignment was
 *          published for, with their submission details
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Anthony Hurst (Hogeschool Gent)
 * @author Bert De Clercq (Hogeschool Gent)
 */
class SubmittersBrowserComponent extends SubmissionsManager implements DelegateComponent, TableSupport
{

    /**
     * The submitter type
     * 
     * @var int
     */
    private $submitter_type;

    /**
     * The assignment content object
     * 
     * @var Assignment
     */
    public $assignment;

    /**
     * All users that need to be shown in the individual submitters list.
     * 
     * @var array \application\weblcms\integration\core\tracking\tracker\AssignmentSubmission
     */
    private $users;

    /**
     * All course groups that need to be shown on the course groups tab.
     * 
     * @var array \application\weblcms\integration\core\tracking\tracker\AssignmentSubmission
     */
    private $course_groups;

    /**
     * All platform groups that need to be shown on the platform groups tab.
     * 
     * @var array \application\weblcms\integration\core\tracking\tracker\AssignmentSubmission
     */
    private $platform_groups;

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    public function run()
    {
        /**
         * @var ContentObjectPublication $publication
         */
        $publication = DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $this->get_publication_id());

        if(empty($publication)) {
            throw new ObjectNotExistException(Translation::get("Publication"), $this->get_publication_id());
        }

        if (! $this->is_allowed(WeblcmsRights::VIEW_RIGHT, $publication) ||
             ! $this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $this->redirect(
                null, 
                false, 
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_STUDENT_BROWSE_SUBMISSIONS, 
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication->getId()));
        }
        
        $this->assignment = $publication->get_content_object();
        
        $breadcrumb_trail = BreadcrumbTrail::getInstance();
        $breadcrumbs = $breadcrumb_trail->get_breadcrumbs();
        $breadcrumbs[$breadcrumb_trail->size() - 1] = new Breadcrumb(
            $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_BROWSE_SUBMITTERS, 
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->get_publication_id())), 
            $this->assignment->get_title());
        $breadcrumb_trail->set_breadcrumbtrail($breadcrumbs);
        
        $this->get_submitters_data();
        return $this->browse_submitters();
    }

    /**
     * Returns the assignment.
     * 
     * @return Assignment
     */
    public function get_assignment()
    {
        return $this->assignment;
    }

    /**
     * Gets the users, course groups and platform groups the assignment was published for plus the number of their
     * submissions and feedbacks
     */
    private function get_submitters_data()
    {
        $publication = DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $this->get_publication_id());
        
        // Users
        $this->users = DataManager::get_publication_target_users_by_publication_id(
            $this->get_publication_id());
        // Course groups
        $this->course_groups = DataManager::retrieve_publication_target_course_groups(
            $this->get_publication_id(), 
            $publication->get_course_id())->as_array();
        // Platform groups
        $this->platform_groups = DataManager::retrieve_publication_target_platform_groups(
            $this->get_publication_id(), 
            $publication->get_course_id())->as_array();
    }

    /**
     * Displays assignment details and tabs with a submitters browser table per submitter type
     */
    public function browse_submitters()
    {
        $html = array();
        
        $html[] = $this->render_header();
        
        $html[] = '<div class="announcements level_1" style="background-image: url(' .
             Theme::getInstance()->getCommonImagePath('ContentObject/Introduction') . ');">';
        $html[] = $this->generate_assignment_details_html();
        $html[] = $this->get_reporting_html();
        $html[] = '</div><br />';
        
        // retrieve group submissions allowed
        $publication = DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $this->get_publication_id());
        
        $assignment = $publication->get_content_object();
        $allow_group_submissions = $assignment->get_allow_group_submissions();
        
        if ($allow_group_submissions == 0)
        {
            $this->submitter_type = AssignmentSubmission::SUBMITTER_TYPE_USER;
            
            $users_table = new SubmissionUsersBrowserTable($this);
            $users_html = $users_table->as_html();
            $html[] = $users_html;
        }
        else
        {
            // Create tabs per submitter type
            $type = Request::get(self::PARAM_TYPE);
            if ($type == null)
            {
                $type = self::TYPE_COURSE_GROUP;
            }
            switch ($type)
            {
                case self::TYPE_COURSE_GROUP :
                    $this->submitter_type = AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP;
                    $course_groups_table = new SubmissionCourseGroupsBrowserTable($this);
                    $content_table = $course_groups_table->as_html();
                    $selected_course_group = true;
                    break;
                case self::TYPE_GROUP :
                    $this->submitter_type = AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP;
                    $groups_table = new SubmissionGroupsBrowserTable($this);
                    $content_table = $groups_table->as_html();
                    $parameters[] = $parameters[self::PARAM_TYPE] = self::TYPE_GROUP;
                    $selected_group = true;
                    break;
            }
            $parameters_course_group = $this->get_parameters();
            $parameters_course_group[self::PARAM_TYPE] = self::TYPE_COURSE_GROUP;
            $link_course_group = $this->get_url($parameters_course_group);
            $parameters_group = $this->get_parameters();
            $parameters_group[self::PARAM_TYPE] = self::TYPE_GROUP;
            $link_group = $this->get_url($parameters_group);
            
            $tabs = new DynamicVisualTabsRenderer('submissions', $content_table);
            $tab_course_group = new DynamicVisualTab(
                'tab_course_group', 
                Translation::get('CourseGroups'), 
                null, 
                $link_course_group, 
                $selected_course_group);
            $tabs->add_tab($tab_course_group);
            $tab_group = new DynamicVisualTab(
                'tab_group', 
                Translation::get('Groups'), 
                null, 
                $link_group, 
                $selected_group);
            $tabs->add_tab($tab_group);
            $html[] = $tabs->render();
        }
        
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the submitter type
     * 
     * @return int
     */
    public function get_submitter_type()
    {
        return $this->submitter_type;
    }

    /**
     * Returns the reporting as an array of strings.
     * 
     * @return array The reporting
     */
    private function get_reporting_html()
    {
        $html = array();
        
        // Reporting title
        $html[] = '<div class="title" style="border-bottom:1px dotted #D3D3D3;width:100%;border-top:1px dotted #D3D3D3;
            padding-top:5px">';
        $html[] = Translation::get('Reporting');
        $html[] = '</div><div class="clear">&nbsp;</div><br />';
        
        $html[] = '<div style="font-weight:bold;float:left">';
        
        if (! $this->assignment->get_allow_group_submissions())
        {
            $html[] = Translation::get('UsersSubmissions') . ':&nbsp;<br />';
            $html[] = Translation::get('UsersFeedback') . ':&nbsp;<br />';
            
            if ($this->assignment->get_allow_late_submissions())
            {
                $html[] = Translation::get('UsersLateSubmissions') . ':&nbsp;<br />';
            }
        }
        else
        {
            $type = Request::get(self::PARAM_TYPE);
            
            if ($type == null)
            {
                $type = self::TYPE_COURSE_GROUP;
            }
            switch ($type)
            {
                case self::TYPE_COURSE_GROUP :
                    $html[] = Translation::get('CourseGroupsSubmissions') . ':&nbsp;<br />';
                    $html[] = Translation::get('CourseGroupsFeedback') . ':&nbsp;<br />';
                    if ($this->assignment->get_allow_late_submissions())
                    {
                        $html[] = Translation::get('CourseGroupsLateSubmissions') . ':&nbsp;<br />';
                    }
                    break;
                
                case self::TYPE_GROUP :
                    $html[] = Translation::get('PlatformGroupsSubmissions') . ':&nbsp;<br />';
                    $html[] = Translation::get('PlatformGroupsFeedback') . ':&nbsp;<br />';
                    if ($this->assignment->get_allow_late_submissions())
                    {
                        $html[] = Translation::get('PlatformGroupsLateSubmissions') . ':&nbsp;<br />';
                    }
                    break;
            }
        }
        $html[] = '</div>';
        
        // Reporting data
        $html[] = '<div style="float:left">';
        $html[] = $this->get_reporting_block_html();
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the reporting data block as an array of strings.
     * 
     * @return array The reporting data block
     */
    private function get_reporting_block_html()
    {
        if (! $this->assignment->get_allow_group_submissions())
        {
            return $this->get_reporting_data_html(AssignmentSubmission::SUBMITTER_TYPE_USER);
        }
        else
        {
            $type = Request::get(self::PARAM_TYPE);
            
            if ($type == null)
            {
                $type = self::TYPE_COURSE_GROUP;
            }
            switch ($type)
            {
                case self::TYPE_COURSE_GROUP :
                    return $this->get_reporting_data_html(AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP);
                case self::TYPE_GROUP :
                    return $this->get_reporting_data_html(AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP);
            }
        }
    }

    /**
     * Returns the reporting data from the specified type as an array of strings.
     * 
     * @param $submitter_type int The type of submitter
     * @return array The reporting data
     */
    private function get_reporting_data_html($submitter_type)
    {
        $count_submissions = 0;
        $count_feedbacks = 0;
        $count_late_submissions = 0;
        
        $submitters = array();
        
        $html = array();
        
        switch ($submitter_type)
        {
            case AssignmentSubmission::SUBMITTER_TYPE_USER :
                $submitters = $this->users;
                break;
            case AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP :
                $submitters = $this->course_groups;
                break;
            case AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP :
                $submitters = $this->platform_groups;
                break;
        }
        
        foreach ($submitters as $submitter)
        {
            
            switch ($submitter_type)
            {
                case AssignmentSubmission::SUBMITTER_TYPE_USER :
                    $submissions_tracker = $this->get_submissions_tracker(
                        $submitter_type, 
                        $submitter[User::PROPERTY_ID]);
                    $feedbacks_tracker = $this->get_submitter_feedbacks(
                        $submitter_type, 
                        $submitter[User::PROPERTY_ID]);
                    break;
                case AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP :
                case AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP :
                    $submissions_tracker = $this->get_submissions_tracker($submitter_type, $submitter->get_id());
                    $feedbacks_tracker = $this->get_submitter_feedbacks($submitter_type, $submitter->get_id());
                    break;
            }
            
            if ($submissions_tracker)
            {
                $count_submissions ++;
                
                if ($submissions_tracker['last_date'] > $this->assignment->get_end_time())
                {
                    $count_late_submissions ++;
                }
            }
            
            if ($feedbacks_tracker)
            {
                $count_feedbacks ++;
            }
        }
        
        $html[] = $count_submissions . '/' . count($submitters) . '<br />';
        $html[] = $count_feedbacks . '/' . count($submitters) . '<br />';
        if ($this->assignment->get_allow_late_submissions())
        {
            $html[] = $count_late_submissions . '/' . count($submitters) . '<br />';
        }
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Gets a toolbar with common actions
     * 
     * @return ButtonToolBarRenderer
     */
    public function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $type = Request::get(self::PARAM_TYPE);
            $url = $this->get_url(array(self::PARAM_TYPE => $type));
            
            $buttonToolbar = new ButtonToolBar($url);
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();
            
            if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
            {
                if ($this->get_submission_trackers_by_publication($this->get_publication_id()))
                {
                    $commonActions->addButton(
                        new Button(
                            Translation::get('DownloadAllSubmissions'), 
                            Theme::getInstance()->getCommonImagePath('Action/Download'), 
                            $this->get_url(
                                array(
                                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DOWNLOAD_SUBMISSIONS)), 
                            ToolbarItem::DISPLAY_ICON_AND_LABEL));
                }
                
                $toolActions->addButton(
                    new Button(
                        Translation::get('ScoresOverview'), 
                        Theme::getInstance()->getCommonImagePath('Action/Statistics'), 
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => \Chamilo\Application\Weblcms\Manager::ACTION_REPORTING, 
                                \Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID => \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\CourseAssignmentSubmittersTemplate::class_name(), 
                                \Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager::ACTION_VIEW)), 
                        ToolbarItem::DISPLAY_ICON_AND_LABEL));
                
                $toolActions->addButton(
                    new Button(
                        Translation::get('SubmissionsOverview'), 
                        Theme::getInstance()->getCommonImagePath('Action/Statistics'), 
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => \Chamilo\Application\Weblcms\Manager::ACTION_REPORTING, 
                                \Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID => \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\AssignmentSubmissionsTemplate::class_name(), 
                                \Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager::ACTION_VIEW)), 
                        ToolbarItem::DISPLAY_ICON_AND_LABEL));
                
                $course = $this->get_course();
                $ephorus_tool = DataManager::retrieve_course_tool_by_name(
                    'Ephorus');
                if ($ephorus_tool && $course->get_course_setting(
                    \Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting::COURSE_SETTING_TOOL_ACTIVE, 
                    $ephorus_tool->get_id()))
                {
                    $toolActions->addButton(
                        new Button(
                            Translation::get('EphorusOverview'), 
                            Theme::getInstance()->getImagePath(
                                \Chamilo\Application\Weblcms\Tool\Manager::get_tool_type_namespace('Ephorus'), 
                                'Logo/16'), 
                            $this->get_url(
                                array(
                                    \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => 'Ephorus', 
                                    \Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION => $this->get_publication_id(), 
                                    \Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager::ACTION_ASSIGNMENT_BROWSER)), 
                            ToolbarItem::DISPLAY_ICON_AND_LABEL));
                }
            }
            
            $commonActions->addButton(
                new Button(
                    Translation::get('SubmissionSubmit'), 
                    Theme::getInstance()->getCommonImagePath('Action/Add'), 
                    $this->get_url($this->generate_add_submission_url()), 
                    ToolbarItem::DISPLAY_ICON_AND_LABEL));
            
            $commonActions->addButton(
                new Button(
                    Translation::get('ShowAll', null, Utilities::COMMON_LIBRARIES), 
                    Theme::getInstance()->getCommonImagePath('Action/Browser'), 
                    $url, 
                    ToolbarItem::DISPLAY_ICON_AND_LABEL, 
                    false));
            
            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);
            
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }
        
        return $this->buttonToolbarRenderer;
    }

    /**
     * Returns the url for the add submission button.
     * When it's not a group assignment the url will have the extra
     * parameters target id and submitter type.
     * 
     * @return array The url
     */
    private function generate_add_submission_url()
    {
        if ($this->assignment->get_allow_group_submissions())
        {
            return array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_SUBMIT_SUBMISSION, 
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->get_publication_id());
        }
        else
        {
            return array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_SUBMIT_SUBMISSION, 
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->get_publication_id(), 
                self::PARAM_TARGET_ID => $this->get_user_id(), 
                self::PARAM_SUBMITTER_TYPE => AssignmentSubmission::SUBMITTER_TYPE_USER);
        }
    }

    /**
     * Returns the condition
     * 
     * @param string $table_class_name
     *
     * @return \libraries\storage\Condition
     */
    public function get_table_condition($table_class_name)
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        
        if (isset($query) && $query != '')
        {
            switch ($this->get_submitter_type())
            {
                case AssignmentSubmission::SUBMITTER_TYPE_USER :
                    $conditions = array();
                    $conditions[] = new PatternMatchCondition(
                        new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME), 
                        '*' . $query . '*');
                    $conditions[] = new PatternMatchCondition(
                        new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME), 
                        '*' . $query . '*');
                    return new OrCondition($conditions);
                case AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP :
                    return new PatternMatchCondition(
                        new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_NAME), 
                        '*' . $query . '*');
                case AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP :
                    return new PatternMatchCondition(
                        new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME), 
                        '*' . $query . '*');
            }
        }
        return null;
    }

    public function render_header()
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

    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, 
            self::PARAM_TARGET_ID, 
            self::PARAM_TYPE);
    }
}
