<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Component\AllSubscribed\AllSubscribedUserTable;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Component\DirectSubscribedGroup\DirectSubscribedPlatformGroupTable;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Component\Group\PlatformGroupRelUserTable;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Component\Subscribed\SubscribedUserTable;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Component\SubSubscribedGroup\SubSubscribedPlatformGroupTable;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\User\SubscribedPlatformGroupMenuRenderer;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonSearchForm;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @author Stijn Van Hoecke
 * @package application.lib.weblcms.tool.user.component
 */
class UnsubscribeBrowserComponent extends Manager implements TableSupport
{
    const PLATFORM_GROUP_ROOT_ID = 0;

    const TAB_ALL = 1;

    const TAB_PLATFORM_GROUPS_SUBGROUPS = 4;

    const TAB_PLATFORM_GROUPS_USERS = 3;

    const TAB_USERS = 2;

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    private $current_tab;

    private $tabs;

    public function run()
    {
        // default all tab, unless specified
        $this->current_tab = self::TAB_ALL;
        if (Request::get(self::PARAM_TAB))
        {
            $this->current_tab = Request::get(self::PARAM_TAB);
        }

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $this->set_parameter(
            ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY, $this->buttonToolbarRenderer->getSearchForm()->getQuery()
        );

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $this->get_tabs();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    // **************************************************************************
    // TABS FUNCTIONS
    // **************************************************************************

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_user_unsubscribe_browser');
    }

    public function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {

            $parameters = [];

            $group_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_GROUP);
            if (isset($group_id))
            {
                $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_GROUP] = $group_id;
            }

            $buttonToolbar = new ButtonToolBar($this->get_url($parameters));
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();

            if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
            {
                $commonActions->addButton(
                    new Button(
                        Translation::get('SubscribeUsers'), new FontAwesomeGlyph('user'),
                        $this->get_url(array(self::PARAM_ACTION => self::ACTION_SUBSCRIBE_USER_BROWSER)),
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );

                $commonActions->addButton(
                    new Button(
                        Translation::get('SubscribeGroups'), new FontAwesomeGlyph('users'),
                        $this->get_url(array(self::PARAM_ACTION => self::ACTION_SUBSCRIBE_GROUP_DETAILS)),
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );

                $param_export_subscriptions_overview = [];
                $param_export_subscriptions_overview[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] =
                    self::ACTION_EXPORT;
                $param_export_subscriptions_overview[self::PARAM_TAB] = $this->current_tab;

                $toolActions->addButton(
                    new Button(
                        Translation::get('ExportUserList'), new FontAwesomeGlyph('download'),
                        $this->get_url($param_export_subscriptions_overview), ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            $show_all_url = $this->get_url();

            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_TAB;

        $current_tab = self::TAB_ALL;
        if (Request::get(self::PARAM_TAB))
        {
            $current_tab = Request::get(self::PARAM_TAB);
        }

        if ($current_tab != self::TAB_ALL && $current_tab != self::TAB_USERS)
        {
            $additionalParameters[] = \Chamilo\Application\Weblcms\Manager::PARAM_GROUP;
        }

        return parent::getAdditionalParameters($additionalParameters);
    }

    private function get_all_users_tab()
    {
        $table = new AllSubscribedUserTable($this);

        return $table->as_html();
    }

    public function get_condition()
    {
        $conditions = [];
        $search_condition = $this->get_search_condition();
        if ($search_condition)
        {
            $conditions[] = $search_condition;
        }

        if ($this->current_tab == self::TAB_USERS)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID
                ), new StaticConditionVariable($this->get_course_id())
            );
        }
        elseif ($this->current_tab == self::TAB_PLATFORM_GROUPS_SUBGROUPS)
        {
            if ($this->get_group() == self::PLATFORM_GROUP_ROOT_ID)
            {
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID
                    ), new StaticConditionVariable($this->get_course_id())
                );
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_TYPE
                    ), new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_GROUP)
                );
            }
            else
            {
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
                    new StaticConditionVariable(Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_GROUP))
                );
            }
        }
        elseif ($this->current_tab == self::TAB_PLATFORM_GROUPS_USERS)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
                new StaticConditionVariable(Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_GROUP))
            );
        }
        if ($conditions)
        {
            return new AndCondition($conditions);
        }

        return null;
    }

    private function get_direct_users_tab()
    {
        $table = new SubscribedUserTable($this);

        return $table->as_html();
    }

    public function get_group()
    {
        $group = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_GROUP);
        if (!$group)
        {
            return self::PLATFORM_GROUP_ROOT_ID;
        }

        return $group;
    }

    // **************************************************************************
    // PLATFORMGROUP TABS FUNCTIONS
    // **************************************************************************

    public function get_menu_tree()
    {
        $root_ids = $this->get_subscribed_platformgroup_ids($this->get_course_id());
        if (count($root_ids) > 0)
        {
            return new SubscribedPlatformGroupMenuRenderer($this, $root_ids, true);
        }

        return null;
    }

    /**
     * Creates the tab structure.
     *
     * @return String HTML of the tab(s)
     */
    private function get_platformgroup_tabs()
    {
        $html = [];

        $tabs = new DynamicVisualTabsRenderer('weblcms_course_user_platformgroups_browser');

        // no users tab if the root is selected
        if ($this->get_group() != self::PLATFORM_GROUP_ROOT_ID)
        {
            // users tab
            $link = $this->get_url(array(self::PARAM_TAB => self::TAB_PLATFORM_GROUPS_USERS));
            $tab_name = Translation::get('Users', null, Utilities::COMMON_LIBRARIES);

            $tabs->add_tab(
                new DynamicVisualTab(
                    self::TAB_PLATFORM_GROUPS_USERS, $tab_name,
                    new FontAwesomeGlyph('user', array('fa-lg'), null, 'fas'), $link,
                    $this->current_tab == self::TAB_PLATFORM_GROUPS_USERS
                )
            );
        }
        else
        {
            // reset tab (users tab doesn't exists)
            if ($this->current_tab == self::TAB_PLATFORM_GROUPS_USERS)
            {
                $this->current_tab = self::TAB_PLATFORM_GROUPS_SUBGROUPS;
            }
        }

        // subgroups tab
        $link = $this->get_url(array(self::PARAM_TAB => self::TAB_PLATFORM_GROUPS_SUBGROUPS));
        if ($this->get_group() != self::PLATFORM_GROUP_ROOT_ID)
        {
            $tab_name = Translation::get('Subgroups', null, Manager::context());
            $tab_selected = $this->current_tab == self::TAB_PLATFORM_GROUPS_SUBGROUPS;
        }
        else
        {
            $tab_name = Translation::get('SubscribedGroups');
            $tab_selected = true;
        }
        $tabs->add_tab(
            new DynamicVisualTab(
                self::TAB_PLATFORM_GROUPS_SUBGROUPS, $tab_name,
                new FontAwesomeGlyph('users', array('fa-lg'), null, 'fas'), $link, $tab_selected
            )
        );

        // render
        $html[] = $tabs->header();
        $html[] = $this->get_platformgroup_tabs_content();
        $html[] = $tabs->footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Creates the content of the selected tab.
     *
     * @return String HTML of the content
     */
    private function get_platformgroup_tabs_content()
    {
        switch ($this->current_tab)
        {
            case self::TAB_PLATFORM_GROUPS_SUBGROUPS :
                return $this->get_platformgroups_subgroups_tab();
            case self::TAB_PLATFORM_GROUPS_USERS :
                return $this->get_platformgroups_users_tab();
        }
    }

    /**
     * Creates the footer for the tabs.
     *
     * @return String HTML of the footer
     */
    private function get_platformgroup_tabs_footer()
    {
        $html = [];
        $html[] = $this->tabs->body_footer();
        $html[] = $this->tabs->footer();

        return implode(PHP_EOL, $html);
    }

    private function get_platformgroups_subgroups_tab()
    {
        // build table
        $parameters = $this->get_parameters();
        $parameters[ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY] =
            $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        if ($this->get_group() != self::PLATFORM_GROUP_ROOT_ID)
        {
            $table = new SubSubscribedPlatformGroupTable($this);
        }
        else
        {
            $table = new DirectSubscribedPlatformGroupTable($this);
        }

        return $table->as_html();
    }

    private function get_platformgroups_tab()
    {
        $menu_tree = $this->get_menu_tree();
        $html = [];
        if ($menu_tree)
        {
            $html[] = '<div class="row">';
            $html[] = '<div class="col-xs-12 col-md-4 col-lg-3">';
            $html[] = $menu_tree->render_as_tree();
            $html[] = '</div>';
            $html[] = '<div class="col-xs-12 col-md-8 col-lg-9">';
            $html[] = $this->get_platformgroup_tabs();
            $html[] = '</div>';
            $html[] = '</div>';
        }
        else
        {
            $html[] = Translation::get('NoGroupsSubscribed');
        }

        return implode(PHP_EOL, $html);
    }

    private function get_platformgroups_users_tab()
    {
        $table = new PlatformGroupRelUserTable($this);

        return $table->as_html();
    }

    public function get_search_condition()
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        if (isset($query) && $query != '')
        {
            $conditions = [];
            switch ($this->current_tab)
            {
                case self::TAB_ALL :
                case self::TAB_USERS :
                    $conditions[] = $this->buttonToolbarRenderer->getConditions(
                        array(
                            new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE),
                            new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME),
                            new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME),
                            new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME),
                            new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL)
                        )
                    );
                    break;
                case self::TAB_PLATFORM_GROUPS_SUBGROUPS :
                    $conditions[] = new ContainsCondition(
                        new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME), $query
                    );
                    $conditions[] = new ContainsCondition(
                        new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE), $query
                    );
                    $conditions[] = new ContainsCondition(
                        new PropertyConditionVariable(Group::class, Group::PROPERTY_DESCRIPTION), $query
                    );
                    break;
                case self::TAB_PLATFORM_GROUPS_USERS :
                    return null;
            }

            return new OrCondition($conditions);
        }

        return null;
    }

    public function get_table_condition($object_table_class_name)
    {
        return $this->get_condition();
    }

    /**
     * Creates the tab structure.
     *
     * @return String HTML of the tab(s)
     */
    private function get_tabs()
    {
        $html = [];

        $html[] = $this->get_tabs_header();
        $html[] = $this->get_tabs_content();
        $html[] = $this->get_tabs_footer();

        return implode(PHP_EOL, $html);
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
            case self::TAB_ALL :
                return $this->get_all_users_tab();
            case self::TAB_USERS :
                return $this->get_direct_users_tab();
            case self::TAB_PLATFORM_GROUPS_SUBGROUPS :
            case self::TAB_PLATFORM_GROUPS_USERS :
                return $this->get_platformgroups_tab();
        }
    }

    /**
     * Creates the footer for the tabs.
     *
     * @return String HTML of the footer
     */
    private function get_tabs_footer()
    {
        $html = [];
        $html[] = $this->tabs->body_footer();
        $html[] = $this->tabs->footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Creates the header for the tabs.
     *
     * @return String HTML of the header
     */
    private function get_tabs_header()
    {
        $html = [];

        $this->tabs = new DynamicVisualTabsRenderer('weblcms_course_user_browser');

        // all tab
        $link = $this->get_url(
            array(self::PARAM_TAB => self::TAB_ALL), array(\Chamilo\Application\Weblcms\Manager::PARAM_GROUP)
        );
        $tab_name = Translation::get('AllSubscriptions');

        $this->tabs->add_tab(
            new DynamicVisualTab(
                self::TAB_ALL, $tab_name, new FontAwesomeGlyph('user', array('fa-lg'), null, 'fas'), $link,
                $this->current_tab == self::TAB_ALL
            )
        );

        // users tab
        $link = $this->get_url(
            array(self::PARAM_TAB => self::TAB_USERS), array(\Chamilo\Application\Weblcms\Manager::PARAM_GROUP)
        );
        $tab_name = Translation::get('DirectSubscriptions');

        $this->tabs->add_tab(
            new DynamicVisualTab(
                self::TAB_USERS, $tab_name, new FontAwesomeGlyph('user', array('fa-lg'), null, 'fas'), $link,
                $this->current_tab == self::TAB_USERS
            )
        );

        // groups tab
        $link = $this->get_url(array(self::PARAM_TAB => self::TAB_PLATFORM_GROUPS_SUBGROUPS));
        $tab_name = Translation::get('GroupSubscriptions');
        $selected = $this->current_tab == self::TAB_PLATFORM_GROUPS_SUBGROUPS ||
            $this->current_tab == self::TAB_PLATFORM_GROUPS_USERS;

        $this->tabs->add_tab(
            new DynamicVisualTab(
                self::TAB_PLATFORM_GROUPS_SUBGROUPS, $tab_name,
                new FontAwesomeGlyph('users', array('fa-lg'), null, 'fas'), $link, $selected
            )
        );

        // render
        $html[] = $this->tabs->header();
        $html[] = $this->tabs->body_header();

        return implode(PHP_EOL, $html);
    }

    public function is_course_admin($user)
    {
        return $this->get_course()->is_course_admin($user);
    }
}
