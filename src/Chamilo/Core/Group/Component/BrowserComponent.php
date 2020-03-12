<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Menu\GroupMenu;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Table\Group\GroupTable;
use Chamilo\Core\Group\Table\GroupRelUser\GroupRelUserTable;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
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
    const TAB_DETAILS = 2;

    const TAB_SUBGROUPS = 0;

    const TAB_USERS = 1;

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    private $group;

    private $rootGroup;

    private $groupIdentifier;

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Exception
     */

    public function run()
    {
        if (!$this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->getButtonToolbarRenderer()->render() . '<br />';
        $html[] = $this->get_user_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('group general');
    }

    public function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $translator = $this->getTranslator();

            $buttonToolbar =
                new ButtonToolBar($this->get_url(array(self::PARAM_GROUP_ID => $this->getGroupIdentifier())));
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    $translator->trans('Add', array(), Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('plus'),
                    $this->get_create_group_url($this->getGroupIdentifier()), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $commonActions->addButton(
                new Button(
                    $translator->trans('Root', array(), Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('home'),
                    $this->get_group_viewing_url($this->getRootGroup()), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $commonActions->addButton(
                new Button(
                    $translator->trans('ShowAll', array(), Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('folder'),
                    $this->get_url(array(self::PARAM_GROUP_ID => $this->getGroupIdentifier())),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );
            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     * @return \Chamilo\Core\Group\Storage\DataClass\Group
     */
    public function getGroup()
    {
        if (!isset($this->group))
        {
            $this->group = $this->retrieve_group($this->getGroupIdentifier());
        }

        return $this->group;
    }

    /**
     * @return integer
     */
    public function getGroupIdentifier()
    {
        if (!$this->groupIdentifier)
        {
            $this->groupIdentifier =
                $this->getRequest()->query->get(self::PARAM_GROUP_ID, $this->getRootGroup()->getId());
        }

        return $this->groupIdentifier;
    }

    /**
     * @param string $query
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\OrCondition
     */
    public function getGroupsCondition($query)
    {
        $conditions = array();

        $conditions[] = new PatternMatchCondition(
            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME), '*' . $query . '*'
        );
        $conditions[] = new PatternMatchCondition(
            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_DESCRIPTION), '*' . $query . '*'
        );
        $conditions[] = new PatternMatchCondition(
            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_CODE), '*' . $query . '*'
        );

        return new OrCondition($conditions);
    }

    /**
     * @return \Chamilo\Core\Group\Storage\DataClass\Group
     */
    public function getRootGroup()
    {
        if (!$this->rootGroup)
        {
            $this->rootGroup = $this->retrieve_groups(
                new EqualityCondition(
                    new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_PARENT_ID),
                    new StaticConditionVariable(0)
                )
            )->next_result();
        }

        return $this->rootGroup;
    }

    /**
     * @return string[]
     */
    public function get_additional_parameters()
    {
        return array(self::PARAM_GROUP_ID);
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\OrCondition
     */
    function get_all_groups_condition()
    {
        $condition = null;

        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        if (isset($query) && $query != '')
        {
            $condition = $this->getGroupsCondition($query);
        }

        return $condition;
    }

    public function get_group_info()
    {
        $group = $this->getGroup();
        $translator = $this->getTranslator();

        $html = array();

        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Edit', array(), Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('pencil'),
                $this->get_group_editing_url($group), ToolbarItem::DISPLAY_ICON_AND_LABEL
            )
        );

        if ($this->getGroup()->getId() != $this->getRootGroup()->getId())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Delete', array(), Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('times'),
                    $this->get_group_delete_url($group), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );
        }

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('AddUsers'), new FontAwesomeGlyph('plus-circle'),
                $this->get_group_suscribe_user_browser_url($group), ToolbarItem::DISPLAY_ICON_AND_LABEL
            )
        );

        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class_name(), GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($group->getId())
        );
        $users = $this->retrieve_group_rel_users($condition);
        $visible = ($users->size() > 0);

        if ($visible)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Truncate'), new FontAwesomeGlyph('trash'),
                    $this->get_group_emptying_url($group), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('TruncateNA'), new FontAwesomeGlyph('trash', array('text-muted')), null,
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );
        }

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Metadata', array(), Utilities::COMMON_LIBRARIES),
                new FontAwesomeGlyph('info-circle'), $this->get_group_metadata_url($group),
                ToolbarItem::DISPLAY_ICON_AND_LABEL
            )
        );

        $html[] = '<b>' . $translator->trans('Code') . '</b>: ' . $group->get_code() . '<br />';

        $description = $group->get_description();

        if ($description)
        {
            $html[] = '<b>' . $translator->trans('Description', array(), Utilities::COMMON_LIBRARIES) . '</b>: ' .
                $description . '<br />';
        }

        $html[] = '<br />';
        $html[] = $toolbar->render();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function get_menu()
    {
        $group_menu = new GroupMenu($this->getGroupIdentifier());

        return $group_menu->render_as_tree();
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    public function get_subgroups_condition()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_PARENT_ID),
            new StaticConditionVariable($this->getGroupIdentifier())
        );

        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        if (isset($query) && $query != '')
        {
            $and_conditions = array();

            $and_conditions[] = $condition;
            $and_conditions[] = $this->getGroupsCondition($query);

            $condition = new AndCondition($and_conditions);
        }

        return $condition;
    }

    /**
     * @param string $table_class_name
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
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

        return null;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function get_user_html()
    {
        $renderer_name = ClassnameUtilities::getInstance()->getClassnameFromObject($this, true);
        $tabs = new DynamicTabsRenderer($renderer_name);
        $translator = $this->getTranslator();

        // Subgroups table tab
        $table = new GroupTable($this);
        $table->setSearchForm($this->buttonToolbarRenderer->getSearchForm());
        $tabs->add_tab(
            new DynamicContentTab(
                self::TAB_SUBGROUPS, $translator->trans('Subgroups'), new FontAwesomeGlyph(
                'users', array('fa-lg'), null, 'fas'
            ), $table->render()
            )
        );

        $table = new GroupRelUserTable($this);
        $table->setSearchForm($this->buttonToolbarRenderer->getSearchForm());
        $tabs->add_tab(
            new DynamicContentTab(
                self::TAB_USERS, $translator->trans('Users', array(), \Chamilo\Core\User\Manager::context()),
                new FontAwesomeGlyph('user', array('fa-lg'), null, 'fas'), $table->render()
            )
        );

        // Group info tab
        $tabs->add_tab(
            new DynamicContentTab(
                self::TAB_DETAILS, $translator->trans('Details'), new FontAwesomeGlyph(
                'info-circle', array('fa-lg'), null, 'fas'
            ), $this->get_group_info()
            )
        );

        return $tabs->render();
    }

    public function get_users_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class_name(), GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($this->getGroupIdentifier())
        );

        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME), '*' . $query . '*'
            );
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME), '*' . $query . '*'
            );
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_USERNAME), '*' . $query . '*'
            );
            $condition = new OrCondition($or_conditions);

            $users = DataManager::retrieves(
                User::class_name(), new DataClassRetrievesParameters($condition)
            );

            $userconditions = array();

            while ($user = $users->next_result())
            {
                $userconditions[] = new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class_name(), GroupRelUser::PROPERTY_USER_ID),
                    new StaticConditionVariable($user->get_id())
                );
            }

            if (count($userconditions))
            {
                $conditions[] = new OrCondition($userconditions);
            }
            else
            {
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class_name(), GroupRelUser::PROPERTY_USER_ID),
                    new StaticConditionVariable(0)
                );
            }
        }

        $condition = new AndCondition($conditions);

        return $condition;
    }

    /**
     * @return boolean
     */
    public function has_menu()
    {
        return true;
    }
}
