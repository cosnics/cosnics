<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Menu\GroupMenu;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\SubscribedUser;
use Chamilo\Core\Group\Table\GroupTableRenderer;
use Chamilo\Core\Group\Table\SubscribedUserTableRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Format\Tabs\ContentTab;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Format\Tabs\TabsRenderer;
use Chamilo\Libraries\Storage\DataClass\NestedSet;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Group\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BrowserComponent extends Manager
{
    public const TAB_DETAILS = 2;
    public const TAB_SUBGROUPS = 0;
    public const TAB_USERS = 1;

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    private ?Group $group;

    private ?string $groupIdentifier;

    private ?Group $rootGroup;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function run()
    {
        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->getButtonToolbarRenderer()->render() . '<br />';
        $html[] = $this->get_user_html();
        $html[] = $this->renderFooter();

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

    public function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $translator = $this->getTranslator();

            $buttonToolbar = new ButtonToolBar($this->get_url([self::PARAM_GROUP_ID => $this->getGroupIdentifier()]));
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
                    $this->get_url([self::PARAM_GROUP_ID => $this->getGroupIdentifier()]),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );
            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function getGroup(): Group
    {
        if (!isset($this->group))
        {
            $this->group = $this->getGroupService()->findGroupByIdentifier($this->getGroupIdentifier());
        }

        return $this->group;
    }

    public function getGroupIdentifier(): string
    {
        if (!$this->groupIdentifier)
        {
            $this->groupIdentifier =
                $this->getRequest()->query->get(self::PARAM_GROUP_ID, $this->getRootGroup()->getId());
        }

        return $this->groupIdentifier;
    }

    /**
     * @throws \QuickformException
     */
    protected function getGroupTableCondition(): ?Condition
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        if (is_null($query))
        {
            return $this->get_subgroups_condition();
        }
        else
        {
            return $this->get_all_groups_condition();
        }
    }

    public function getGroupTableRenderer(): GroupTableRenderer
    {
        return $this->getService(GroupTableRenderer::class);
    }

    public function getGroupsCondition(string $query): OrCondition
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

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    public function getRootGroup(): Group
    {
        if (!$this->rootGroup)
        {
            $this->rootGroup = $this->getGroupService()->findRootGroup();
        }

        return $this->rootGroup;
    }

    public function getSubscribedUserTableRenderer(): SubscribedUserTableRenderer
    {
        return $this->getService(SubscribedUserTableRenderer::class);
    }

    /**
     * @throws \QuickformException
     */
    public function getSubscribedUsersCondition(): ?OrCondition
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        if (isset($query) && $query != '')
        {
            $conditions = [];

            $conditions[] = new ContainsCondition(
                new PropertyConditionVariable(SubscribedUser::class, User::PROPERTY_FIRSTNAME), $query
            );
            $conditions[] = new ContainsCondition(
                new PropertyConditionVariable(SubscribedUser::class, User::PROPERTY_LASTNAME), $query
            );
            $conditions[] = new ContainsCondition(
                new PropertyConditionVariable(SubscribedUser::class, User::PROPERTY_USERNAME), $query
            );

            return new OrCondition($conditions);
        }

        return null;
    }

    protected function getTabsRenderer(): TabsRenderer
    {
        return $this->getService(TabsRenderer::class);
    }

    /**
     * @throws \QuickformException
     */
    public function get_all_groups_condition(): ?OrCondition
    {
        $condition = null;

        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        if (isset($query) && $query != '')
        {
            $condition = $this->getGroupsCondition($query);
        }

        return $condition;
    }

    public function get_group_info(): string
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

        $subscribedUserCount =
            $this->getGroupMembershipService()->countSubscribedUsersForGroupIdentifier($group->getId());

        $visible = ($subscribedUserCount > 0);

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
                    $translator->trans('TruncateNA'), new FontAwesomeGlyph('trash-alt', ['text-muted']), null,
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
    public function get_menu(): string
    {
        $group_menu = new GroupMenu($this->getGroupIdentifier());

        return $group_menu->render_as_tree();
    }

    /**
     * @throws \QuickformException
     */
    public function get_subgroups_condition(): Condition
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_PARENT_ID),
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
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \ReflectionException
     * @throws \QuickformException
     */
    public function get_user_html(): string
    {
        $renderer_name = ClassnameUtilities::getInstance()->getClassnameFromObject($this, true);
        $tabs = new TabsCollection();
        $translator = $this->getTranslator();

        // Subgroups table tab
        $tabs->add(
            new ContentTab(
                (string) self::TAB_SUBGROUPS, $translator->trans('Subgroups'), $this->renderGroupTable(),
                new FontAwesomeGlyph(
                    'users', ['fa-lg'], null, 'fas'
                )
            )
        );

        $tabs->add(
            new ContentTab(
                (string) self::TAB_USERS, $translator->trans('Users', [], \Chamilo\Core\User\Manager::CONTEXT),
                $this->renderSubscribedUsertable(), new FontAwesomeGlyph('user', ['fa-lg'], null, 'fas')
            )
        );

        // Group info tab
        $tabs->add(
            new ContentTab(
                (string) self::TAB_DETAILS, $translator->trans('Details'), $this->get_group_info(),
                new FontAwesomeGlyph(
                    'info-circle', ['fa-lg'], null, 'fas'
                )
            )
        );

        return $this->getTabsRenderer()->render($renderer_name, $tabs);
    }

    public function has_menu(): bool
    {
        return true;
    }

    /**
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function renderGroupTable(): string
    {
        $totalNumberOfItems = $this->getGroupService()->countGroups($this->getGroupTableCondition());
        $groupTableRenderer = $this->getGroupTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $groupTableRenderer->getParameterNames(), $groupTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $users = $this->getGroupService()->findGroups(
            $this->getGroupTableCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $groupTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $groupTableRenderer->render($tableParameterValues, $users);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderSubscribedUsertable(): string
    {
        $totalNumberOfItems = $this->getGroupMembershipService()->countSubscribedUsersForGroupIdentifier(
            $this->getGroupIdentifier(), $this->getSubscribedUsersCondition()
        );
        $subscribedUserTableRenderer = $this->getSubscribedUserTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $subscribedUserTableRenderer->getParameterNames(),
            $subscribedUserTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $users = $this->getGroupMembershipService()->findSubscribedUsersForGroupIdentifier(
            $this->getGroupIdentifier(), $this->getSubscribedUsersCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $subscribedUserTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $subscribedUserTableRenderer->render($tableParameterValues, $users);
    }
}
