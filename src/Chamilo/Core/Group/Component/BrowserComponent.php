<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Menu\GroupMenu;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Table\Group\GroupTable;
use Chamilo\Core\Group\Table\GroupRelUser\GroupRelUserTable;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package group.lib.group_manager.component
 */
/**
 * Weblcms component which allows the user to manage his or her user subscriptions
 */
class BrowserComponent extends Manager implements TableSupport
{
    const TAB_SUBGROUPS = 0;
    const TAB_USERS = 1;
    const TAB_DETAILS = 2;

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    private $group;

    private $root_group;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $menu = $this->get_menu_html();
        $output = $this->get_user_html();

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->buttonToolbarRenderer->render() . '<br />';
        $html[] = $menu;
        $html[] = $output;
        $html[] = $this->render_footer();

        $this->getGroupService()->testClosureTable();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return GroupService
     */
    protected function getGroupService()
    {
        return $this->getService(GroupService::class);
    }

    public function get_user_html()
    {
        $html = array();
        $html[] = '<div style="float: right; width: 80%;">';

        $renderer_name = ClassnameUtilities::getInstance()->getClassnameFromObject($this, true);
        $tabs = new DynamicTabsRenderer($renderer_name);

        $subgroup_count = $this->count_groups($this->get_subgroups_condition());
        $user_count = $this->count_group_rel_users($this->get_users_condition());

        // Subgroups table tab
        // if ($subgroup_count > 0)
        // {
        $table = new GroupTable($this);
        $table->setSearchForm($this->buttonToolbarRenderer->getSearchForm());
        $tabs->add_tab(
            new DynamicContentTab(
                self::TAB_SUBGROUPS,
                Translation::get('Subgroups'),
                Theme::getInstance()->getImagePath(\Chamilo\Core\Group\Manager::context(), 'Logo/' . Theme::ICON_MINI),
                $table->as_html()));

        $table = new GroupRelUserTable($this);
        $table->setSearchForm($this->buttonToolbarRenderer->getSearchForm());
        $tabs->add_tab(
            new DynamicContentTab(
                self::TAB_USERS,
                Translation::get('Users', null, \Chamilo\Core\User\Manager::context()),
                Theme::getInstance()->getImagePath(\Chamilo\Core\User\Manager::context(), 'Logo/' . Theme::ICON_MINI),
                $table->as_html()));

        // Group info tab
        $tabs->add_tab(
            new DynamicContentTab(
                self::TAB_DETAILS,
                Translation::get('Details'),
                Theme::getInstance()->getImagePath('Chamilo\Core\Help', 'Logo/' . Theme::ICON_MINI),
                $this->get_group_info()));

        $html[] = $tabs->render();

        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';

        return implode($html, "\n");
    }

    public function get_menu_html()
    {
        $group_menu = new GroupMenu($this->get_group());
        // $group_menu = new TreeMenu('GroupTreeMenu', new GroupTreeMenuDataProvider($this->get_url(),
        // $this->get_group()));
        $html = array();
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        $html[] = $group_menu->render_as_tree();
        $html[] = '</div>';

        return implode($html, "\n");
    }

    public function get_group()
    {
        if (! $this->group)
        {
            $this->group = Request::get(self::PARAM_GROUP_ID);

            if (! $this->group)
            {
                $this->group = $this->get_root_group()->get_id();
            }
        }

        return $this->group;
    }

    public function get_root_group()
    {
        if (! $this->root_group)
        {
            $group = $this->retrieve_groups(
                new EqualityCondition(
                    new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_PARENT_ID),
                    new StaticConditionVariable(0)))->next_result();
            $this->root_group = $group;
        }

        return $this->root_group;
    }

    public function get_subgroups_condition()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_PARENT_ID),
            new StaticConditionVariable($this->get_group()));

        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        if (isset($query) && $query != '')
        {
            $or_conditions = array();
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME),
                '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_DESCRIPTION),
                '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_CODE),
                '*' . $query . '*');
            $or_condition = new OrCondition($or_conditions);

            $and_conditions = array();
            $and_conditions[] = $condition;
            $and_conditions[] = $or_condition;
            $condition = new AndCondition($and_conditions);
        }

        return $condition;
    }

    function get_all_groups_condition()
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        if (isset($query) && $query != '')
        {
            $or_conditions = array();
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME),
                '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_DESCRIPTION),
                '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_CODE),
                '*' . $query . '*');
            $condition = new OrCondition($or_conditions);
        }

        return $condition;
    }

    public function get_users_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class_name(), GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($this->get_group()));

        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME),
                '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME),
                '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_USERNAME),
                '*' . $query . '*');
            $condition = new OrCondition($or_conditions);

            $users = \Chamilo\Core\User\Storage\DataManager::retrieves(
                \Chamilo\Core\User\Storage\DataClass\User::class_name(),
                new DataClassRetrievesParameters($condition));
            while ($user = $users->next_result())
            {
                $userconditions[] = new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class_name(), GroupRelUser::PROPERTY_USER_ID),
                    new StaticConditionVariable($user->get_id()));
            }

            if (count($userconditions))
            {
                $conditions[] = new OrCondition($userconditions);
            }
            else
            {
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class_name(), GroupRelUser::PROPERTY_USER_ID),
                    new StaticConditionVariable(0));
            }
        }

        $condition = new AndCondition($conditions);

        return $condition;
    }

    public function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url(array(self::PARAM_GROUP_ID => $this->get_group())));
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    Translation::get('Add', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Add'),
                    $this->get_create_group_url($this->get_group()),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL));

            $commonActions->addButton(
                new Button(
                    Translation::get('Root', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Home'),
                    $this->get_group_viewing_url($this->get_root_group()),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL));

            $commonActions->addButton(
                new Button(
                    Translation::get('ShowAll', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Browser'),
                    $this->get_url(array(self::PARAM_GROUP_ID => $this->get_group())),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL));
            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function get_group_info()
    {
        $group_id = $this->get_group();
        $group = $this->retrieve_group($group_id);

        $html = array();

        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Edit', null, Utilities::COMMON_LIBRARIES),
                Theme::getInstance()->getCommonImagePath('Action/Edit'),
                $this->get_group_editing_url($group),
                ToolbarItem::DISPLAY_ICON_AND_LABEL));

        if ($this->group != $this->root_group)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Delete', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Delete'),
                    $this->get_group_delete_url($group),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL));
        }

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('AddUsers'),
                Theme::getInstance()->getCommonImagePath('Action/Subscribe'),
                $this->get_group_suscribe_user_browser_url($group),
                ToolbarItem::DISPLAY_ICON_AND_LABEL));

        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class_name(), GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($group->get_id()));
        $users = $this->retrieve_group_rel_users($condition);
        $visible = ($users->size() > 0);

        if ($visible)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Truncate'),
                    Theme::getInstance()->getCommonImagePath('Action/RecycleBin'),
                    $this->get_group_emptying_url($group),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('TruncateNA'),
                    Theme::getInstance()->getCommonImagePath('Action/RecycleBinNa'),
                    null,
                    ToolbarItem::DISPLAY_ICON_AND_LABEL));
        }

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Metadata', null, Utilities::COMMON_LIBRARIES),
                Theme::getInstance()->getCommonImagePath('Action/Metadata'),
                $this->get_group_metadata_url($group),
                ToolbarItem::DISPLAY_ICON_AND_LABEL));

        $html[] = '<b>' . Translation::get('Code') . '</b>: ' . $group->get_code() . '<br />';

        $description = $group->get_description();
        if ($description)
        {
            $html[] = '<b>' . Translation::get('Description', null, Utilities::COMMON_LIBRARIES) . '</b>: ' .
                 $description . '<br />';
        }

        $html[] = '<br />';
        $html[] = $toolbar->as_html();

        return implode(PHP_EOL, $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('group general');
    }

    /*
     * (non-PHPdoc) @see \libraries\format\TableSupport::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
        switch ($table_class_name)
        {
            case GroupTable::class_name() :
                $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();

                if (is_null($query))
                {
                    return $this->get_subgroups_condition();
                }
                else
                {
                    return $this->get_all_groups_condition();
                }

            case GroupRelUserTable::class_name() :
                return $this->get_users_condition();
        }
    }

    public function get_additional_parameters()
    {
        return array(self::PARAM_GROUP_ID);
    }
}
