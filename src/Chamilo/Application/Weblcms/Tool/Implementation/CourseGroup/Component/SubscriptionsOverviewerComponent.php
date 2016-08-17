<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\Overview\CourseUser\CourseUsersTable;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\Overview\GroupUser\CourseGroupUserTable;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;
use Chamilo\Libraries\Utilities\Utilities;

class SubscriptionsOverviewerComponent extends Manager implements TableSupport
{
    const TAB_USERS = 1;
    const TAB_COURSE_GROUPS = 2;
    const PLATFORM_GROUP_ROOT_ID = 0;

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    private $current_tab;

    /**
     * Temporary variable for condition building
     *
     * @var int
     */
    private $table_course_group_id;

    public function run()
    {
        if (! $this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $this->current_tab = self :: TAB_COURSE_GROUPS;
        if (Request :: get(self :: PARAM_TAB))
        {
            $this->current_tab = Request :: get(self :: PARAM_TAB);
        }

        $html = array();

        $html[] = $this->render_header();

        $course_settings_controller = CourseSettingsController :: get_instance();

        if ($course_settings_controller->get_course_setting(
            $this->get_course(),
            \Chamilo\Application\Weblcms\CourseSettingsConnector :: ALLOW_INTRODUCTION_TEXT))
        {
            $html[] = $this->display_introduction_text($this->get_introduction_text());
        }

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $this->get_tabs();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    // **************************************************************************
    // TABS FUNCTIONS
    // **************************************************************************
    /**
     * Creates the tab structure.
     *
     * @return String HTML of the tab(s)
     */
    private function get_tabs()
    {
        $tabs = new DynamicVisualTabsRenderer('weblcms_course_user_browser');

        // all tab
        $link = $this->get_url(array(self :: PARAM_TAB => self :: TAB_USERS));
        $tab_name = Translation :: get('User');
        $tabs->add_tab(
            new DynamicVisualTab(
                self :: TAB_USERS,
                $tab_name,
                Theme :: getInstance()->getCommonImagePath('Place/Users'),
                $link,
                $this->current_tab == self :: TAB_USERS));

        // users tab
        $link = $this->get_url(array(self :: PARAM_TAB => self :: TAB_COURSE_GROUPS));
        $tab_name = Translation :: get('CourseGroup');
        $tabs->add_tab(
            new DynamicVisualTab(
                self :: TAB_COURSE_GROUPS,
                $tab_name,
                Theme :: getInstance()->getCommonImagePath('Place/User'),
                $link,
                $this->current_tab == self :: TAB_COURSE_GROUPS));

        $tabs->set_content($this->get_tabs_content());

        return $tabs->render();
    }

    /**
     * Creates the content of the selected tab.
     *
     * @return String HTML of the content
     */
    private function get_tabs_content()
    {
        switch ($this->current_tab)
        {
            case self :: TAB_USERS :
                return $this->get_users_tab();
                break;
            case self :: TAB_COURSE_GROUPS :
                return $this->get_course_groups_tab();
                break;
        }
    }

    private function get_users_tab()
    {
        $table = new CourseUsersTable($this);
        return $table->as_html();
    }

    /**
     * Handles the content for the course groups tab
     *
     * @return string
     */
    private function get_course_groups_tab()
    {
        $courseGroupRoot = DataManager :: retrieve_course_group_root($this->get_course_id());
        $course_groups = $courseGroupRoot->get_children(false);

        return $this->handle_course_groups($course_groups);
    }

    /**
     * Handles a resultset of course groups and their children
     *
     * @param ResultSet $course_groups
     *
     * @return string
     */
    protected function handle_course_groups(ResultSet $course_groups)
    {
        $html = array();

        while ($course_group = $course_groups->next_result())
        {
            $this->table_course_group_id = $course_group->get_id();

            $table = new CourseGroupUserTable($this);
            $html[] = '<h4>' . $course_group->get_name() . '</h4>' . $table->as_html();

            $children = $course_group->get_children(false);
            $html[] = $this->handle_course_groups($children);
        }

        return implode("\n", $html);
    }

    public function get_table_course_group_id()
    {
        return $this->table_course_group_id;
    }

    public function get_group()
    {
        $group = Request :: get(\Chamilo\Application\Weblcms\Manager :: PARAM_GROUP);
        if (! $group)
        {
            return self :: PLATFORM_GROUP_ROOT_ID;
        }
        return $group;
    }

    public function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $parameters = array();

            $buttonToolbar = new ButtonToolBar($this->get_url($parameters));
            $commonActions = new ButtonGroup();

            $param_export_subscriptions_overview[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION] = self :: ACTION_EXPORT_SUBSCRIPTIONS_OVERVIEW;
            $param_export_subscriptions_overview[self :: PARAM_TAB] = $this->current_tab;

            // $show_all_url = $this->get_url();

            if ($this->is_allowed(WeblcmsRights :: VIEW_RIGHT))
            {
                $commonActions->addButton(
                    new Button(
                        Translation :: get('Export', null, Utilities :: COMMON_LIBRARIES),
                        new BootstrapGlyph('download'),
                        $this->get_url($param_export_subscriptions_overview),
                        ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }

            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function get_condition()
    {
        $conditions = array();
        $search_condition = $this->get_search_condition();
        if ($search_condition)
        {
            $conditions[] = $search_condition;
        }

        if ($conditions)
        {
            return new AndCondition($conditions);
        }
        return null;
    }

    public function get_search_condition()
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        if (isset($query) && $query != '')
        {
            if ($this->current_tab == self :: TAB_USERS)
            {
                $conditions = array();

                $conditions[] = new PatternMatchCondition(
                    new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_OFFICIAL_CODE),
                    '*' . $query . '*');
                $conditions[] = new PatternMatchCondition(
                    new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_LASTNAME),
                    '*' . $query . '*');
                $conditions[] = new PatternMatchCondition(
                    new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_FIRSTNAME),
                    '*' . $query . '*');
                $conditions[] = new PatternMatchCondition(
                    new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_USERNAME),
                    '*' . $query . '*');
                $conditions[] = new PatternMatchCondition(
                    new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_EMAIL),
                    '*' . $query . '*');
                return new OrCondition($conditions);
            }
        }
        return null;
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_TAB, \Chamilo\Application\Weblcms\Manager :: PARAM_GROUP);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_course_groups_overview');
        $this->addBrowserBreadcrumb($breadcrumbtrail);
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
        return $this->get_search_condition();
    }
}
