<?php
namespace Chamilo\Core\Group\Component;

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
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonGroup;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\MiniButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonToolBarSearchFormTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\MiniButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\ContentTab;
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

    public const TAB_DETAILS = 2;
    public const TAB_SUBGROUPS = 0;
    public const TAB_USERS = 1;

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
    public function run(): Response
    {
        if (!$this->getUser()->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $this->setButtonToolBarSearchFormRequestQuery();

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->getButtonToolBarRenderer()->render($this->getButtonToolBar()) . '<br />';
        $html[] = $this->renderTabs();
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getButtonToolBar(): ButtonToolBar
    {
        $translator = $this->getTranslator();

        $buttonToolBar = new ButtonToolBar(
            $this->getUrlGenerator()->fromParameters(
                [
                    self::PARAM_CONTEXT => Manager::CONTEXT,
                    self::PARAM_ACTION => self::ACTION_BROWSE,
                    self::PARAM_GROUP_ID => $this->getGroupIdentifier()
                ]
            )
        );
        $commonActions = new ButtonGroup();

        $commonActions->addButton(
            new Button(
                $translator->trans('Add', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('plus'),
                $this->getGroupUrlGenerator()->getCreateUrl($this->getGroup()), DisplayTypeEnum::ICON_AND_LABEL
            )
        );

        $commonActions->addButton(
            new Button(
                $translator->trans('Root', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('home'),
                $this->getGroupUrlGenerator()->getViewUrl($this->getRootGroup()), DisplayTypeEnum::ICON_AND_LABEL
            )
        );

        $commonActions->addButton(
            new Button(
                $translator->trans('ShowAll', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('folder'),
                $this->getUrlGenerator()->fromParameters(
                    [
                        self::PARAM_CONTEXT => Manager::CONTEXT,
                        self::PARAM_ACTION => self::ACTION_BROWSE,
                        self::PARAM_GROUP_ID => $this->getGroupIdentifier()
                    ]
                ), DisplayTypeEnum::ICON_AND_LABEL
            )
        );
        $buttonToolBar->addButton($commonActions);

        return $buttonToolBar;
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
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \QuickformException
     */
    public function getGroupDetails(): string
    {
        $group = $this->getGroup();
        $translator = $this->getTranslator();

        $html = [];

        $buttonToolBar = new MiniButtonToolBar();

        $buttonToolBar->addButton(
            new Button(
                label: $translator->trans('Edit', [], StringUtilities::LIBRARIES), inlineGlyph: new FontAwesomeGlyph(
                'pencil-alt'
            ), action: $this->getGroupUrlGenerator()->getUpdateUrl($group), display: DisplayTypeEnum::ICON_AND_LABEL,
                classes: ['btn-link']
            )
        );

        if ($this->getGroup()->getId() != $this->getRootGroup()->getId()) {
            $buttonToolBar->addButton(
                new Button(
                    label: $translator->trans('Delete', [], StringUtilities::LIBRARIES),
                    inlineGlyph: new FontAwesomeGlyph('times'), action: $this->getGroupUrlGenerator()->getDeleteUrl(
                    $group
                ), display: DisplayTypeEnum::ICON_AND_LABEL, classes: ['btn-link']
                )
            );
        }

        $buttonToolBar->addButton(
            new Button(
                label: $translator->trans('AddUsers'), inlineGlyph: new FontAwesomeGlyph('plus-circle'),
                action: $this->getGroupUrlGenerator()->getSubscribeUrl($group),
                display: DisplayTypeEnum::ICON_AND_LABEL, classes: ['btn-link']
            )
        );

        $subscribedUserCount =
            $this->getGroupMembershipService()->countSubscribedUsersForGroupIdentifier($group->getId());

        $visible = ($subscribedUserCount > 0);

        if ($visible) {
            $buttonToolBar->addButton(
                new Button(
                    label: $translator->trans('Truncate'), inlineGlyph: new FontAwesomeGlyph('trash-alt'),
                    action: $this->getGroupUrlGenerator()->getTruncateUrl($group),
                    display: DisplayTypeEnum::ICON_AND_LABEL, classes: ['btn-link']
                )
            );
        }
        else {
            $buttonToolBar->addButton(
                new Button(
                    label: $translator->trans('TruncateNA'), inlineGlyph: new FontAwesomeGlyph(
                    'trash-alt', ['text-muted']
                ), display: DisplayTypeEnum::ICON_AND_LABEL, classes: ['btn-link']
                )
            );
        }

        $html[] = '<b>' . $translator->trans('Code') . '</b>: ' . $group->getCode() . '<br />';

        $description = $group->getDescription();

        if ($description) {
            $html[] =
                '<b>' . $translator->trans('Description', [], StringUtilities::LIBRARIES) . '</b>: ' . $description .
                '<br />';
        }

        $html[] = '<br />';
        $html[] = $this->getMiniButtonToolBarRenderer()->render($buttonToolBar);

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

    public function getSubscribedUserTableRenderer(): SubscribedUserTableRenderer
    {
        return $this->getService(SubscribedUserTableRenderer::class);
    }

    public function getSubscribedUsersCondition(): ?AndCondition
    {
        return $this->getButtonToolBarSearchCondition(SubscribedUser::class);
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function renderHeader(): string
    {
        $html = [];

        $html[] = parent::renderHeader();
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
                Application::PARAM_ACTION => Manager::ACTION_GROUP_TREE_DATA,
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
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
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

        return $this->getTabsRenderer()->renderNavigationAndContent('group_browser', $tabs);
    }
}
