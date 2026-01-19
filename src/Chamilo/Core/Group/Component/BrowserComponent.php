<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\SubscribedUser;
use Chamilo\Core\Group\UserInterface\Menu\GroupMenu;
use Chamilo\Core\Group\UserInterface\Table\GroupTableRenderer;
use Chamilo\Core\Group\UserInterface\Table\SubscribedUserTableRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBarSearchFormTrait;
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
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Group\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BrowserComponent extends Manager
{
    use ButtonToolBarSearchFormTrait;

    public const TAB_DETAILS = 2;
    public const TAB_SUBGROUPS = 0;
    public const TAB_USERS = 1;

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    private ?Group $group;

    private ?string $groupIdentifier = null;

    private ?Group $rootGroup;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function run(): Response
    {
        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $this->setButtonToolBarSearchFormRequestQuery();

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->getButtonToolbarRenderer()->render() . '<br />';
        $html[] = $this->renderTabs();
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    public function getButtonToolBarSearchProperties(?string $type = null): array
    {
        $searchProperties = [];

        if ($type === Group::class)
        {
            $searchProperties[] = new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME);
            $searchProperties[] = new PropertyConditionVariable(Group::class, Group::PROPERTY_DESCRIPTION);
            $searchProperties[] = new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE);
        }
        elseif ($type === SubscribedUser::class)
        {
            $searchProperties[] = new PropertyConditionVariable(SubscribedUser::class, User::PROPERTY_FIRSTNAME);
            $searchProperties[] = new PropertyConditionVariable(SubscribedUser::class, User::PROPERTY_LASTNAME);
            $searchProperties[] = new PropertyConditionVariable(SubscribedUser::class, User::PROPERTY_USERNAME);
        }

        return $searchProperties;
    }

    public function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $translator = $this->getTranslator();

            $buttonToolbar = new ButtonToolBar(
                $this->getUrlGenerator()->fromParameters(
                    [
                        self::PARAM_CONTEXT => Manager::CONTEXT,
                        self::PARAM_ACTION => self::ACTION_BROWSER,
                        self::PARAM_GROUP_ID => $this->getGroupIdentifier()
                    ]
                )
            );
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    $translator->trans('Add', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('plus'),
                    $this->getGroupUrlGenerator()->getCreateUrl($this->getGroup()), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $commonActions->addButton(
                new Button(
                    $translator->trans('Root', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('home'),
                    $this->getGroupUrlGenerator()->getViewUrl($this->getRootGroup()),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $commonActions->addButton(
                new Button(
                    $translator->trans('ShowAll', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('folder'),
                    $this->getUrlGenerator()->fromParameters(
                        [
                            self::PARAM_CONTEXT => Manager::CONTEXT,
                            self::PARAM_ACTION => self::ACTION_BROWSER,
                            self::PARAM_GROUP_ID => $this->getGroupIdentifier()
                        ]
                    ), ToolbarItem::DISPLAY_ICON_AND_LABEL
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

    public function getGroupDetails(): string
    {
        $group = $this->getGroup();
        $translator = $this->getTranslator();

        $html = [];

        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        $toolbar->addItem(
            new ToolbarItem(
                $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $this->getGroupUrlGenerator()->getUpdateUrl($group), ToolbarItem::DISPLAY_ICON_AND_LABEL
            )
        );

        if ($this->getGroup()->getId() != $this->getRootGroup()->getId())
        {
            $toolbar->addItem(
                new ToolbarItem(
                    $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $this->getGroupUrlGenerator()->getDeleteUrl($group), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );
        }

        $toolbar->addItem(
            new ToolbarItem(
                $translator->trans('AddUsers'), new FontAwesomeGlyph('plus-circle'),
                $this->getGroupUrlGenerator()->getSubscribeUrl($group), ToolbarItem::DISPLAY_ICON_AND_LABEL
            )
        );

        $subscribedUserCount =
            $this->getGroupMembershipService()->countSubscribedUsersForGroupIdentifier($group->getId());

        $visible = ($subscribedUserCount > 0);

        if ($visible)
        {
            $toolbar->addItem(
                new ToolbarItem(
                    $translator->trans('Truncate'), new FontAwesomeGlyph('trash-alt'),
                    $this->getGroupUrlGenerator()->getTruncateUrl($group), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );
        }
        else
        {
            $toolbar->addItem(
                new ToolbarItem(
                    $translator->trans('TruncateNA'), new FontAwesomeGlyph('trash-alt', ['text-muted']), null,
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );
        }

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

    public function getGroupIdentifier(): string
    {
        if (!isset($this->groupIdentifier))
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
        $conditions = [];

        $searchCondition = $this->getButtonToolBarSearchCondition(Group::class);

        if ($searchCondition instanceof Condition)
        {
            $conditions[] = $searchCondition;
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_PARENT_ID),
            new StaticConditionVariable($this->getGroupIdentifier())
        );

        return new AndCondition($conditions);
    }

    public function getGroupTableRenderer(): GroupTableRenderer
    {
        return $this->getService(GroupTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    public function getRootGroup(): Group
    {
        if (!isset($this->rootGroup))
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
    public function getSubscribedUsersCondition(): ?AndCondition
    {
        return $this->getButtonToolBarSearchCondition(SubscribedUser::class);
    }

    protected function getTabsRenderer(): TabsRenderer
    {
        return $this->getService(TabsRenderer::class);
    }

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function renderApplicationMenu(): string
    {
        $url = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Application::ACTION_BROWSER,
                self::PARAM_GROUP_ID => '%s'
            ]
        );

        $group_menu = new GroupMenu($this->getGroupIdentifier(), $url);

        return $group_menu->render_as_tree();
    }

    public function renderFooter(): string
    {
        $html = [];

        $html[] = '</div>';
        $html[] = parent::renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function renderGroupTable(): string
    {
        $groupTableCondition = $this->getGroupTableCondition();

        $totalNumberOfItems = $this->getGroupService()->countGroups($groupTableCondition);
        $groupTableRenderer = $this->getGroupTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $groupTableRenderer->getParameterNames(), $groupTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $users = $this->getGroupService()->findGroups(
            $groupTableCondition, $tableParameterValues->getOffset(), $tableParameterValues->getNumberOfItemsPerPage(),
            $groupTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $groupTableRenderer->render($tableParameterValues, $users);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function renderHeader(string $pageTitle = ''): string
    {
        $html = [];

        $html[] = parent::renderHeader();
        $html[] = '<div class="col-xs-12 col-md-4 col-lg-3">';
        $html[] = $this->renderApplicationMenu();
        $html[] = '</div>';
        $html[] = '<div class="col-xs-12 col-md-8 col-lg-9">';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderSubscribedUsertable(): string
    {
        $subscribedUsersCondition = $this->getButtonToolBarSearchCondition(SubscribedUser::class);

        $totalNumberOfItems = $this->getGroupMembershipService()->countSubscribedUsersForGroupIdentifier(
            $this->getGroupIdentifier(), $subscribedUsersCondition
        );
        $subscribedUserTableRenderer = $this->getSubscribedUserTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $subscribedUserTableRenderer->getParameterNames(),
            $subscribedUserTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $users = $this->getGroupMembershipService()->findSubscribedUsersForGroupIdentifier(
            $this->getGroupIdentifier(), $subscribedUsersCondition, $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $subscribedUserTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $subscribedUserTableRenderer->render($tableParameterValues, $users);
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function renderTabs(): string
    {
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
                (string) self::TAB_DETAILS, $translator->trans('Details'), $this->getGroupDetails(),
                new FontAwesomeGlyph(
                    'info-circle', ['fa-lg'], null, 'fas'
                )
            )
        );

        return $this->getTabsRenderer()->render('group_browser', $tabs);
    }
}
