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
use Chamilo\Libraries\Format\Tabs\ContentTab;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Format\Tabs\TabsRenderer;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package group.lib.group_manager.component
 */

/**
 * Weblcms component which allows the user to manage his or her user subscriptions
 */
class BrowserComponent extends Manager implements TableSupport
{
    public const TAB_DETAILS = 2;
    public const TAB_SUBGROUPS = 0;
    public const TAB_USERS = 1;

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    private $group;

    private $groupIdentifier;

    private $rootGroup;

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

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->getButtonToolbarRenderer()->render() . '<br />';
        $html[] = $this->get_user_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string[]
     */
    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_GROUP_ID;

        return parent::getAdditionalParameters($additionalParameters);
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
                    $translator->trans('Add', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('plus'),
                    $this->get_create_group_url($this->getGroupIdentifier()), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $commonActions->addButton(
                new Button(
                    $translator->trans('Root', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('home'),
                    $this->get_group_viewing_url($this->getRootGroup()), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $commonActions->addButton(
                new Button(
                    $translator->trans('ShowAll', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('folder'),
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
     * @return int
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
        $conditions = [];

        $conditions[] = new ContainsCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME), $query
        );
        $conditions[] = new ContainsCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_DESCRIPTION), $query
        );
        $conditions[] = new ContainsCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE), $query
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
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
                    new StaticConditionVariable(0)
                )
            )->current();
        }

        return $this->rootGroup;
    }

    protected function getTabsRenderer(): TabsRenderer
    {
        return $this->getService(TabsRenderer::class);
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\OrCondition
     */
    public function get_all_groups_condition()
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

        $html = [];

        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $this->get_group_editing_url($group), ToolbarItem::DISPLAY_ICON_AND_LABEL
            )
        );

        if ($this->getGroup()->getId() != $this->getRootGroup()->getId())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
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
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($group->getId())
        );
        $users = $this->retrieve_group_rel_users($condition);
        $visible = ($users->count() > 0);

        if ($visible)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Truncate'), new FontAwesomeGlyph('trash-alt'),
                    $this->get_group_emptying_url($group), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('TruncateNA'), new FontAwesomeGlyph('trash-alt', array('text-muted')), null,
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );
        }

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Metadata', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('info-circle'),
                $this->get_group_metadata_url($group), ToolbarItem::DISPLAY_ICON_AND_LABEL
            )
        );

        $html[] = '<b>' . $translator->trans('Code') . '</b>: ' . $group->get_code() . '<br />';

        $description = $group->get_description();

        if ($description)
        {
            $html[] =
                '<b>' . $translator->trans('Description', [], StringUtilities::LIBRARIES) . '</b>: ' . $description .
                '<br />';
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
            new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
            new StaticConditionVariable($this->getGroupIdentifier())
        );

        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        if (isset($query) && $query != '')
        {
            $and_conditions = [];

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
            case GroupTable::class :
                $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();

                if (is_null($query))
                {
                    return $this->get_subgroups_condition();
                }
                else
                {
                    return $this->get_all_groups_condition();
                }

            case GroupRelUserTable::class :
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
        $tabs = new TabsCollection();
        $translator = $this->getTranslator();

        // Subgroups table tab
        $table = new GroupTable($this);
        $table->setSearchForm($this->buttonToolbarRenderer->getSearchForm());
        $tabs->add(
            new ContentTab(
                self::TAB_SUBGROUPS, $translator->trans('Subgroups'), $table->render(), new FontAwesomeGlyph(
                    'users', array('fa-lg'), null, 'fas'
                )
            )
        );

        $table = new GroupRelUserTable($this);
        $table->setSearchForm($this->buttonToolbarRenderer->getSearchForm());
        $tabs->add(
            new ContentTab(
                self::TAB_USERS, $translator->trans('Users', [], \Chamilo\Core\User\Manager::context()),
                $table->render(), new FontAwesomeGlyph('user', array('fa-lg'), null, 'fas')
            )
        );

        // Group info tab
        $tabs->add(
            new ContentTab(
                self::TAB_DETAILS, $translator->trans('Details'), $this->get_group_info(), new FontAwesomeGlyph(
                    'info-circle', array('fa-lg'), null, 'fas'
                )
            )
        );

        return $this->getTabsRenderer()->render($renderer_name, $tabs);
    }

    public function get_users_condition()
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($this->getGroupIdentifier())
        );

        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new ContainsCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME), $query
            );
            $or_conditions[] = new ContainsCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME), $query
            );
            $or_conditions[] = new ContainsCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME), $query
            );
            $condition = new OrCondition($or_conditions);

            $users = DataManager::retrieves(
                User::class, new DataClassRetrievesParameters($condition)
            );

            $userconditions = [];

            foreach ($users as $user)
            {
                $userconditions[] = new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
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
                    new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
                    new StaticConditionVariable(0)
                );
            }
        }

        $condition = new AndCondition($conditions);

        return $condition;
    }

    /**
     * @return bool
     */
    public function has_menu()
    {
        return true;
    }
}
