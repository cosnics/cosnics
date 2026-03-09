<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\SubscribedUser;
use Chamilo\Core\Group\UserInterface\Menu\GroupTreeMenuDataProvider;
use Chamilo\Core\Group\UserInterface\Table\GroupTableRenderer;
use Chamilo\Core\Group\UserInterface\Table\SubscribedUserTableRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\NestedSet;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonToolBarSearchFormTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\MiniButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\ContentTab;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\LinkTab;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection;
use Chamilo\Libraries\UserInterface\Tab\Service\TabsRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\UserInterface\Tree\Service\JsTreeRenderer;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Group\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BrowseComponent extends Manager
{
    use ButtonToolBarSearchFormTrait;

    public const TAB_DETAILS = 'details';
    public const TAB_SUBGROUPS = 'subgroups';
    public const TAB_USERS = 'users';

    protected int $numberOfGroups;

    protected int $numberOfSubscribedUsers;

    private ?Group $group;

    private ?string $groupIdentifier = null;

    private ?Group $rootGroup;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $this->setButtonToolBarSearchFormRequestQuery();

        $html = [];

        $html[] = $this->renderHeader($currentUser);
        $html[] = $this->renderTabs();
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    protected function countNumberOfGroups(): int
    {
        if (!isset($this->numberOfGroups)) {
            return $this->getGroupsTreeTraverser()->countSubGroupsForGroup($this->getGroup());
        }

        return $this->numberOfGroups;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    protected function countNumberOfSubscribedUsers(): int
    {
        if (!isset($this->numberOfSubscribedUsers)) {
            $this->numberOfSubscribedUsers =
                $this->getGroupMembershipService()->countSubscribedUsersForGroupIdentifier($this->getGroupIdentifier());
        }

        return $this->numberOfSubscribedUsers;
    }

    public function getButtonToolBarSearchProperties(?string $type = null): array
    {
        $searchProperties = [];

        if ($type === Group::class) {
            $searchProperties[] = new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME);
            $searchProperties[] = new PropertyConditionVariable(Group::class, Group::PROPERTY_DESCRIPTION);
            $searchProperties[] = new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE);
        }
        elseif ($type === SubscribedUser::class) {
            $searchProperties[] = new PropertyConditionVariable(SubscribedUser::class, User::PROPERTY_GIVEN_NAME);
            $searchProperties[] = new PropertyConditionVariable(SubscribedUser::class, User::PROPERTY_SURNAME);
            $searchProperties[] = new PropertyConditionVariable(SubscribedUser::class, User::PROPERTY_USERNAME);
        }

        return $searchProperties;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getGroup(): Group
    {
        if (!isset($this->group)) {
            $this->group = $this->getGroupService()->findGroupByIdentifier($this->getGroupIdentifier());
        }

        return $this->group;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getGroupDetails(): string
    {
        $group = $this->getGroup();
        $translator = $this->getTranslator();

        $html = [];

        $html[] = '<table class="table table-striped table-bordered table-hover">';
        $html[] = ' <tbody>';
        $html[] = '<tr>';
        $html[] = '<th class="w-25" scope="row">' . $translator->trans('Name', [], Manager::CONTEXT) . '</th>';
        $html[] = '<td>' . $group->getName() . '</td>';
        $html[] = '</tr>';
        $html[] = '<tr>';
        $html[] = '<th class="w-25" scope="row">' . $translator->trans('Code', [], Manager::CONTEXT) . '</th>';
        $html[] = '<td>' . $group->getCode() . '</td>';
        $html[] = '</tr>';
        $html[] = '<tr>';
        $html[] = '<th class="w-25" scope="row">' . $translator->trans('Description', [], Manager::CONTEXT) . '</th>';
        $html[] = '<td>' . ($group->getDescription() ? $group->getDescription() : '-') . '</td>';
        $html[] = '</tr>';
        $html[] = '</tbody>';
        $html[] = '</table>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getGroupIdentifier(): string
    {
        if (!isset($this->groupIdentifier)) {
            $this->groupIdentifier =
                $this->getRequest()->query->get(self::PARAM_GROUP_ID, $this->getRootGroup()->getId());
        }

        return $this->groupIdentifier;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    protected function getGroupTableCondition(): ?ConditionInterface
    {
        $conditions = [];

        $searchCondition = $this->getButtonToolBarSearchCondition(Group::class);

        if ($searchCondition instanceof ConditionInterface) {
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

    public function getGroupTreeMenuDataProvider(): GroupTreeMenuDataProvider
    {
        return $this->getService(GroupTreeMenuDataProvider::class);
    }

    public function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return $this->getService(GroupsTreeTraverser::class);
    }

    public function getJsTreeRenderer(): JsTreeRenderer
    {
        return $this->getService(JsTreeRenderer::class);
    }

    public function getMiniButtonToolBarRenderer(): MiniButtonToolBarRenderer
    {
        return $this->getService(MiniButtonToolBarRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getRootGroup(): Group
    {
        if (!isset($this->rootGroup)) {
            $this->rootGroup = $this->getGroupService()->findRootGroup();
        }

        return $this->rootGroup;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getSubGroupsToolBar(): ButtonToolBar
    {
        $translator = $this->getTranslator();

        $buttonToolBar = new ButtonToolBar(
            $this->getUrlGenerator()->fromParameters(
                [
                    self::PARAM_CONTEXT => Manager::CONTEXT,
                    self::PARAM_ACTION => ActionEnum::BROWSE->value,
                    self::PARAM_GROUP_ID => $this->getGroupIdentifier()
                ]
            )
        );

        $buttonToolBar->addButton(
            new Button(
                $translator->trans('Add', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('plus'),
                $this->getGroupUrlGenerator()->getCreateUrl($this->getGroup()), DisplayTypeEnum::ICON_AND_LABEL
            )
        );

        return $buttonToolBar;
    }

    public function getSubscribedUserTableRenderer(): SubscribedUserTableRenderer
    {
        return $this->getService(SubscribedUserTableRenderer::class);
    }

    public function getSubscribedUsersCondition(): ?AndCondition
    {
        return $this->getButtonToolBarSearchCondition(SubscribedUser::class);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getSubscribedUsersToolBar(): ButtonToolBar
    {
        $buttonToolBar = new ButtonToolBar(
            $this->getUrlGenerator()->fromParameters(
                [
                    self::PARAM_CONTEXT => Manager::CONTEXT,
                    self::PARAM_ACTION => ActionEnum::BROWSE->value,
                    self::PARAM_GROUP_ID => $this->getGroupIdentifier()
                ]
            )
        );

        $buttonToolBar->addButton(
            new Button(
                label: $this->getTranslator()->trans('AddUsers'), inlineGlyph: new FontAwesomeGlyph('plus-circle'),
                action: $this->getGroupUrlGenerator()->getSubscribeUrl($this->getGroup()),
                display: DisplayTypeEnum::ICON_AND_LABEL
            )
        );

        return $buttonToolBar;
    }

    protected function getTabsRenderer(): TabsRenderer
    {
        return $this->getService(TabsRenderer::class);
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
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
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

        $html = [];

        $html[] = $this->getButtonToolBarRenderer()->render($this->getSubGroupsToolBar());
        $html[] = $groupTableRenderer->render($tableParameterValues, $users);

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function renderHeader(?User $user = null): string
    {
        $html = [];

        $html[] = parent::renderHeader($user);
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="row">';
        $html[] = '<div class="col-12 col-md-4 col-lg-3">';
        $html[] = $this->renderMenu();
        $html[] = '</div>';
        $html[] = '<div class="col-12 col-md-8 col-lg-9">';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function renderMenu(): string
    {
        $dataUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => 'Chamilo\\\Core\\\Group',
                Application::PARAM_ACTION => ActionEnum::GROUP_TREE_DATA->value,
            ]

        );

        $selectedPathIdentifiers =
            $this->getGroupsTreeTraverser()->findParentGroupIdentifiersForGroup($this->getGroup());

        return $this->getJsTreeRenderer()->render(
            'groupMenu', Manager::PARAM_GROUP_ID, $dataUrl, $selectedPathIdentifiers
        );
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    protected function renderSubscribedUsertable(): string
    {
        $searchCondition = $this->getButtonToolBarSearchCondition(SubscribedUser::class);

        $totalNumberOfItems = $this->getGroupMembershipService()->countSubscribedUsersForGroupIdentifier(
            $this->getGroupIdentifier(), $searchCondition
        );
        $subscribedUserTableRenderer = $this->getSubscribedUserTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $subscribedUserTableRenderer->getParameterNames(),
            $subscribedUserTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $users = $this->getGroupMembershipService()->findSubscribedUsersForGroupIdentifier(
            $this->getGroupIdentifier(), $searchCondition, $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $subscribedUserTableRenderer->determineOrderBy($tableParameterValues)
        );

        $html = [];

        $html[] = $this->getButtonToolBarRenderer()->render($this->getSubscribedUsersToolBar());
        $html[] = $subscribedUserTableRenderer->render($tableParameterValues, $users);

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function renderTabs(): string
    {
        $translator = $this->getTranslator();
        $group = $this->getGroup();

        $tabs = new TabsCollection();
        $selectedTab = self::TAB_DETAILS;

        if ($this->countNumberOfGroups() > 0) {
            $selectedTab = self::TAB_SUBGROUPS;
            $tabs->add(
                new ContentTab(
                    self::TAB_SUBGROUPS, $translator->trans('Subgroups', [], Manager::CONTEXT),
                    $this->renderGroupTable(), new FontAwesomeGlyph(
                        'users', ['fa-lg'], null, 'fas'
                    )
                )
            );
        }
        else {
            $tabs->add(
                new LinkTab(
                    identifier: ActionEnum::CREATE->value, label: $translator->trans('AddGroup', [], Manager::CONTEXT),
                    inlineGlyph: new FontAwesomeGlyph('plus'), link: $this->getGroupUrlGenerator()->getCreateUrl(
                    $this->getGroup()
                ), display: DisplayTypeEnum::ICON_AND_LABEL
                )
            );
        }

        if ($this->countNumberOfSubscribedUsers() > 0) {
            $tabs->add(
                new ContentTab(
                    self::TAB_USERS, $translator->trans('Users', [], \Chamilo\Core\User\Manager::CONTEXT),
                    $this->renderSubscribedUsertable(), new FontAwesomeGlyph('user', ['fa-lg'], null, 'fas')
                )
            );

            $tabs->add(
                new LinkTab(
                    identifier: ActionEnum::TRUNCATE->value, label: $translator->trans('Truncate'),
                    inlineGlyph: new FontAwesomeGlyph(
                        'trash-alt'
                    ), link: $this->getGroupUrlGenerator()->getTruncateUrl($group),
                    display: DisplayTypeEnum::ICON_AND_LABEL, classes: ['text-danger']
                )
            );
        }
        else {
            $tabs->add(
                new LinkTab(
                    identifier: ActionEnum::BROWSE_NON_SUBSCRIBED_USERS->value, label: $translator->trans(
                    'AddUsers', [], Manager::CONTEXT
                ), inlineGlyph: new FontAwesomeGlyph('plus-circle'), link: $this->getGroupUrlGenerator()
                    ->getSubscribeUrl($this->getGroup()), display: DisplayTypeEnum::ICON_AND_LABEL
                )
            );
        }

        $tabs->add(
            new ContentTab(
                self::TAB_DETAILS, $translator->trans('Details'), $this->getGroupDetails(), new FontAwesomeGlyph(
                    'info-circle', ['fa-lg'], null, 'fas'
                )
            )
        );

        $tabs->add(
            new LinkTab(
                identifier: ActionEnum::UPDATE->value, label: $translator->trans('Edit', [], StringUtilities::LIBRARIES
            ), inlineGlyph: new FontAwesomeGlyph('pencil-alt'), link: $this->getGroupUrlGenerator()->getUpdateUrl(
                $group
            ), display: DisplayTypeEnum::ICON_AND_LABEL
            )
        );

        if ($this->getGroup()->getId() != $this->getRootGroup()->getId()) {
            $deleteUrl = $this->getGroupUrlGenerator()->getDeleteUrl($group);
            $tabs->add(
                new LinkTab(
                    identifier: 'Delete', label: $translator->trans('Delete', [], StringUtilities::LIBRARIES),
                    inlineGlyph: new FontAwesomeGlyph('times'), link: $deleteUrl,
                    display: DisplayTypeEnum::ICON_AND_LABEL, classes: ['text-danger']
                )
            );
        }

        return $this->getTabsRenderer()->renderNavigationAndContent('group_browser', $tabs, $selectedTab);
    }
}
