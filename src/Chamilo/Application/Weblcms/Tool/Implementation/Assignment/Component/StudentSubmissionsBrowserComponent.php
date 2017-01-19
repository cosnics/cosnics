<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionCourseGroupBrowser\SubmissionCourseGroupsBrowserTable;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionGroupsBrowser\SubmissionGroupsBrowserTable;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionUsersBrowser\SubmissionUsersBrowserTable;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmitterSubmissions\StudentSubmissionsOwnGroupsTable;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmitterSubmissions\SubmitterUserSubmissionsTable;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager as CourseGroupDataManager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
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
use Chamilo\Libraries\Storage\Cache\DataClassCache;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Displays the students' version of the assignments browser.
 *
 * @author Anthony Hurst (Hogeschool Gent)
 * @author Bert De Clercq (Hogeschool Gent)
 */
class StudentSubmissionsBrowserComponent extends SubmissionsManager implements TableSupport
{
    const PARAM_SELECTED_TAB = 'tab';
    const SELECTED_TAB_OTHER_COURSE_GROUPS = 'other_course_groups';
    const SELECTED_TAB_OTHER_PLATFORM_GROUPS = 'other_platform_groups';
    const SELECTED_TAB_OTHER_USERS = 'other_users';
    const SELECTED_TAB_OWN = 'own';

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    private $assignment;

    public function run()
    {
        $this->define_class_variables();

        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(),
            $this->get_publication_id()
        );

        if (!$this->is_allowed(WeblcmsRights::VIEW_RIGHT, $publication) ||
            !$this->is_allowed(WeblcmsRights::VIEW_RIGHT) || $this->is_allowed(WeblcmsRights::EDIT_RIGHT)
        )
        {
            $this->redirect(
                Translation::get("NotAllowed", null, Utilities::COMMON_LIBRARIES),
                true,
                array(),
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
                )
            );
        }
        $this->modify_last_breadcrumb();

        return $this->display_page();
    }

    /**
     * Modifies the last breadcrumb added to the trail for the page.
     */
    private function modify_last_breadcrumb()
    {
        $breadcrumb_trail = BreadcrumbTrail::getInstance();
        $breadcrumbs = $breadcrumb_trail->get_breadcrumbs();
        $breadcrumbs[$breadcrumb_trail->size() - 1] = new Breadcrumb(
            $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_STUDENT_BROWSE_SUBMISSIONS,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->get_publication_id()
                )
            ),
            $this->assignment->get_title()
        );
        $breadcrumb_trail->set_breadcrumbtrail($breadcrumbs);
    }

    /**
     * Governs the order in which a page is built.
     */
    private function display_page()
    {
        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->display_assignment();
        $html[] = $this->display_submissions_tabs();

        return implode(PHP_EOL, $html);
    }

    /**
     * Displays the page's header.
     */
    public function render_header()
    {
        $html = array();

        $html[] = parent::render_header();

        if ($this->buttonToolbarRenderer)
        {
            $html[] = $this->buttonToolbarRenderer->render();
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Displays the assignment details and possible reporting.
     */
    private function display_assignment()
    {
        $html = array();
        $html[] = '<div class="announcements level_1" style="background-image:url(' .
            Theme::getInstance()->getCommonImagePath('ContentObject/Introduction') . ')">';
        $html[] = $this->generate_assignment_details_html();
        $html[] = $this->get_reporting_as_html();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Displays the body of the page with tabs and tables.
     */
    private function display_submissions_tabs()
    {
        $tabs = new DynamicVisualTabsRenderer('submissions', $this->define_tab_content());
        $tabs->add_tab(
            new DynamicVisualTab(
                'tab_my_submissions',
                $this->define_own_tab_title(),
                null,
                $this->define_tab_url(self::SELECTED_TAB_OWN),
                $this->is_selected_tab(self::SELECTED_TAB_OWN)
            )
        );
        if (!$this->assignment->get_allow_group_submissions())
        {
            $tabs->add_tab(
                new DynamicVisualTab(
                    'tab_other_users',
                    Translation::get('OtherUsers'),
                    null,
                    $this->define_tab_url(self::SELECTED_TAB_OTHER_USERS),
                    $this->is_selected_tab(self::SELECTED_TAB_OTHER_USERS)
                )
            );
        }
        else
        {
            $tabs->add_tab(
                new DynamicVisualTab(
                    'tab_other_course_groups',
                    Translation::get('OtherCourseGroups'),
                    null,
                    $this->define_tab_url(self::SELECTED_TAB_OTHER_COURSE_GROUPS),
                    $this->is_selected_tab(self::SELECTED_TAB_OTHER_COURSE_GROUPS)
                )
            );
            $tabs->add_tab(
                new DynamicVisualTab(
                    'tab_other_platform_groups',
                    Translation::get('OtherPlatformGroups'),
                    null,
                    $this->define_tab_url(self::SELECTED_TAB_OTHER_PLATFORM_GROUPS),
                    $this->is_selected_tab(self::SELECTED_TAB_OTHER_PLATFORM_GROUPS)
                )
            );
        }

        return $tabs->render();
    }

    /**
     * Defines the own submission tab title depending on whether the assignment is a group assignment or not.
     *
     * @return type the title of the tab.
     */
    private function define_own_tab_title()
    {
        if (!$this->assignment->get_allow_group_submissions())
        {
            return Translation::get('MySubmissions');
        }

        return Translation::get('OurSubmissions');
    }

    /**
     * Defines what is displayed within a tab.
     *
     * @return the content to be displayed in the tab in HTML.
     */
    private function define_tab_content()
    {
        switch ($this->get_selected_tab())
        {
            case self::SELECTED_TAB_OWN :
                $table = $this->define_own_tab_content();
                break;
            case self::SELECTED_TAB_OTHER_COURSE_GROUPS :
                $table = new SubmissionCourseGroupsBrowserTable($this);
                break;
            case self::SELECTED_TAB_OTHER_PLATFORM_GROUPS :
                // ******************************************************************************************//
                // DMTODO Problem with caching. Once caching problems fixed,
                // remove the following line of code. //
                // ******************************************************************************************//
                DataClassCache::truncate(Group::class_name());
                // ******************************************************************************************//
                // DMTODO End remove code. //
                // ******************************************************************************************//
                $table = new SubmissionGroupsBrowserTable($this);
                break;
            case self::SELECTED_TAB_OTHER_USERS :
                $table = new SubmissionUsersBrowserTable($this);
                break;
        }

        return $table->as_html();
    }

    /**
     * Defines the content of the own tab for the possibilities individual and groups based on the selected tab.
     *
     * @return ObjectTable the table to be added to the tab.
     */
    private function define_own_tab_content()
    {
        if (!$this->assignment->get_allow_group_submissions())
        {
            return new SubmitterUserSubmissionsTable($this);
        }

        // Faulty table for groups.
        return new StudentSubmissionsOwnGroupsTable($this);
    }

    /**
     * Defines the url that the tab is linked to.
     *
     * @return the url link of the tab.
     */
    private function define_tab_url($selected_tab)
    {
        return $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->get_publication_id(),
                self::PARAM_SELECTED_TAB => $selected_tab
            )
        );
    }

    /**
     * Returns the reporting block as HTML code.
     * This block contains the automatic feedback of the assignment and the
     * average score of the user or group.
     *
     * @return array The HTML code
     */
    private function get_reporting_as_html()
    {
        $title = array();
        if ($this->is_feedback_visible($this->assignment, $this->has_submissions()))
        {
            $title[] = Translation::get('AutomaticFeedback');
        }
        $title[] = Translation::get('Reporting');

        $html = array();

        $html[] = '<div class="title" style="border-top:1px dotted #D3D3D3;padding-top:5px;width:100%;">';
        $html[] = implode(' & ', $title);
        $html[] = '</div><div class="clear">&nbsp;</div>';

        if ($this->is_feedback_visible($this->assignment, $this->has_submissions()))
        {
            $html[] = $this->generate_automatic_feedback_html();
        }

        if (!$this->is_feedback_visible($this->assignment, $this->has_submissions()))
        {
            $html[] = '<br />';
        }

        $html[] = '<div style="font-weight:bold;float:left;">';
        if ($this->assignment->get_allow_group_submissions())
        {
            $html[] = Translation::get('GroupAverageScore') . ':&nbsp;<br />';
        }
        else
        {
            $html[] = Translation::get('MyAverageScore') . ':&nbsp;<br />';
        }
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
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::count_data(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(
            ),
            null,
            $this->get_own_table_conditions()
        ) > 0;
    }

    /**
     * Returns the reporting data as an array of strings.
     *
     * @return array The reporting data
     */
    private function get_reporting_data_as_html()
    {
        $count = 0;
        $total_score = 0;

        $submissions =
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::get_data(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(
                ),
                null,
                $this->get_own_table_conditions(),
                null,
                null,
                null
            )->as_array();

        foreach ($submissions as $submission)
        {
            $score_tracker = $this->get_score_tracker_for_submission($submission->get_id());

            if ($score_tracker)
            {
                $total_score += $score_tracker->get_score();
                $count ++;
            }
        }

        $html = array();
        if ($total_score == 0)
        {
            $html[] = '-';
        }
        else
        {
            $html[] = round($total_score / $count, 2) . '%<br />';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Constructs the action bar visible at the top of the content.
     *
     * @return ButtonToolBarRenderer the action bar to be displayed.
     */
    private function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $search_url = $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->get_publication_id(),
                    self::PARAM_SELECTED_TAB => $this->get_selected_tab()
                )
            );

            $buttonToolbar = new ButtonToolBar($search_url);
            $commonActions = new ButtonGroup();
            $commonActions->addButton(
                new Button(
                    Translation::get('SubmissionSubmit'),
                    Theme::getInstance()->getCommonImagePath('Action/Add'),
                    $this->get_url($this->generate_add_submission_url(), ToolbarItem::DISPLAY_ICON_AND_LABEL)
                )
            );

            $commonActions->addButton(
                new Button(
                    Translation::get('ShowAll', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Browser'),
                    $search_url,
                    ToolbarItem::DISPLAY_ICON_AND_LABEL,
                    false
                )
            );
            $buttonToolbar->addButtonGroup($commonActions);

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
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->get_publication_id()
            );
        }
        else
        {
            return array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_SUBMIT_SUBMISSION,
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->get_publication_id(),
                self::PARAM_TARGET_ID => $this->get_user_id(),
                self::PARAM_SUBMITTER_TYPE => \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER
            );
        }
    }

    /**
     * Retrieves the search entered by the user and presents it as a condition to be used in a database query.
     *
     * @return Condition the search query entered by the user.
     */
    private function get_search_condition()
    {
        if ($this->buttonToolbarRenderer->getSearchForm()->getQuery() != '')
        {
            switch ($this->get_selected_tab())
            {
                case self::SELECTED_TAB_OWN :
                    return $this->define_own_search_conditions();
                case self::SELECTED_TAB_OTHER_COURSE_GROUPS :
                    return new PatternMatchCondition(
                        new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_NAME),
                        '*' . $this->buttonToolbarRenderer->getSearchForm()->getQuery() . '*'
                    );
                case self::SELECTED_TAB_OTHER_PLATFORM_GROUPS :
                    return new PatternMatchCondition(
                        new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_NAME),
                        '*' . $this->buttonToolbarRenderer->getSearchForm()->getQuery() . '*'
                    );
                case self::SELECTED_TAB_OTHER_USERS :
                    $or_conditions = array();
                    $or_conditions[] = new PatternMatchCondition(
                        new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME),
                        '*' . $this->buttonToolbarRenderer->getSearchForm()->getQuery() . '*'
                    );
                    $or_conditions[] = new PatternMatchCondition(
                        new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME),
                        '*' . $this->buttonToolbarRenderer->getSearchForm()->getQuery() . '*'
                    );

                    return new OrCondition($or_conditions);
            }
        }

        return null;
    }

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, self::PARAM_SELECTED_TAB);
    }

    /**
     * TODO.
     *
     * @return null
     */
    private function define_own_search_conditions()
    {
        return null;
    }

    /**
     * Constructs the necessary conditions to limit the rows of the own tab to own submissions.
     *
     * @return \libraries\storage\AndCondition the aggregate of the conditions.
     */
    private function get_own_table_conditions()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(
                ),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_PUBLICATION_ID
            ),
            new StaticConditionVariable($this->get_publication_id())
        );

        if (!$this->assignment->get_allow_group_submissions())
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(
                    ),
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_ID
                ),
                new StaticConditionVariable($this->get_user_id())
            );
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(
                    ),
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_TYPE
                ),
                new StaticConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER
                )
            );
        }
        else
        {
            $group_conditions = array();
            $course_group_conditions = array();
            $course_group_conditions[] = new InCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(
                    ),
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_ID
                ),
                $this->get_user_course_group_ids($this->get_user_id())
            );
            $course_group_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(
                    ),
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_TYPE
                ),
                new StaticConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP
                )
            );
            $group_conditions[] = new AndCondition($course_group_conditions);

            $platform_group_conditions = array();
            $platform_group_conditions[] = new InCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(
                    ),
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_ID
                ),
                $this->get_user_platform_group_ids($this->get_user_id())
            );
            $platform_group_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(
                    ),
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_TYPE
                ),
                new StaticConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP
                )
            );
            $group_conditions[] = new AndCondition($platform_group_conditions);

            $conditions[] = new OrCondition($group_conditions);
        }

        return new AndCondition($conditions);
    }

    /**
     * Retrieves the ids of the course groups of which a user is a member.
     *
     * @param $user_id type The id of the user.
     *
     * @return type an array of the ids of the found course groups.
     */
    private function get_user_course_group_ids($user_id)
    {
        $course_groups = CourseGroupDataManager::retrieve_course_groups_from_user($user_id)->as_array();
        $course_group_ids = array();
        foreach ($course_groups as $course_group)
        {
            $course_group_ids[] = $course_group->get_id();
        }
        // If no groups found, provide a non-existent group id.
        if (count($course_group_ids) == 0)
        {
            $course_group_ids[] = - 1;
        }

        return $course_group_ids;
    }

    /**
     * Retrieves the ids of the platform groups of which a user is a member.
     *
     * @param $user_id type the id of the user.
     *
     * @return type an array of the ids of the found platform groups.
     */
    private function get_user_platform_group_ids($user_id)
    {
        $platform_groups = \Chamilo\Core\Group\Storage\DataManager::retrieve_user_groups($user_id)->as_array();
        $platform_group_ids = array();
        foreach ($platform_groups as $platform_group)
        {
            $platform_group_ids[] = $platform_group->get_id();
        }
        // If no groups found, provide a non-existent group id.
        if (count($platform_group_ids) == 0)
        {
            $platform_group_ids[] = - 1;
        }

        return $platform_group_ids;
    }

    /**
     * Retrieves the selected tab from the url.
     *
     * @return type the selected tab.
     */
    private function get_selected_tab()
    {
        if (!Request::get(self::PARAM_SELECTED_TAB))
        {
            return self::SELECTED_TAB_OWN;
        }

        return Request::get(self::PARAM_SELECTED_TAB);
    }

    /**
     * Tests whether the expected tab is the current tab.
     *
     * @param $expected_tab type the tab expected.
     *
     * @return type whether the expected tab is the current tab.
     */
    private function is_selected_tab($expected_tab)
    {
        return $expected_tab == $this->get_selected_tab();
    }

    /**
     * Initialises the class variables obtained from the database, so as to reduce lookups.
     */
    protected function define_class_variables()
    {
        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(),
            $this->get_publication_id()
        );

        if (!$publication instanceof ContentObjectPublication)
        {
            throw new ObjectNotExistException(
                Translation::getInstance()->getTranslation(
                    'ContentObjectPublication', null, 'Chamilo\Application\Weblcms'
                ), $this->get_publication_id()
            );
        }

        $this->assignment = $publication->get_content_object();

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
    }

    /**
     * Gets the assignment.
     *
     * @return the assignment.
     */
    public function get_assignment()
    {
        return $this->assignment;
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
        switch ($table_class_name)
        {
            case SubmitterUserSubmissionsTable::class_name() :
                $conditions = array();

                if ($this->buttonToolbarRenderer->getSearchForm()->getQuery() != '')
                {
                    $condition = $this->get_search_condition();

                    if ($condition instanceof Condition)
                    {
                        $conditions[] = $condition;
                    }
                }

                $conditions[] = $this->get_own_table_conditions();

                return new AndCondition($conditions);
            case StudentSubmissionsOwnGroupsTable::class_name() :
                $conditions = array();

                if ($this->buttonToolbarRenderer->getSearchForm()->getQuery() != '')
                {
                    $condition = $this->get_search_condition();

                    if ($condition instanceof Condition)
                    {
                        $conditions[] = $condition;
                    }
                }

                $conditions[] = $this->get_own_table_conditions();

                return new AndCondition($conditions);
        }

        return $this->get_search_condition();
    }
}
