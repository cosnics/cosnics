<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\User\PlatformgroupMenuRenderer;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Table\UnsubscribedGroupTableRenderer;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\NestedSet;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\User\Component
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupSubscribeBrowserComponent extends Manager
{

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    private string $groupId;

    private ?Group $rootGroup;

    /**
     * @var int[]
     */
    private array $subscribedGroups;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function run()
    {
        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $this->subscribedGroups = $this->get_subscribed_platformgroup_ids($this->get_course_id());
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->buttonToolbarRenderer->render();

        $html[] = $this->renderInformationMessage();

        $html[] = '<div class="row">';
        $html[] = $this->renderGroupMenu();
        $html[] = $this->renderCurrentGroup();
        $html[] = '</div>';

        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_TAB;
        $additionalParameters[] = \Chamilo\Application\Weblcms\Manager::PARAM_GROUP;

        return parent::getAdditionalParameters($additionalParameters);
    }

    protected function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    $this->getTranslator()->trans('ViewSubscribedUsers'), new FontAwesomeGlyph('folder'),
                    $this->get_url([self::PARAM_ACTION => self::ACTION_UNSUBSCRIBE_BROWSER]),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     * @throws \ReflectionException
     */
    protected function getCurrentGroup(): ?Group
    {
        $groupId = $this->getGroupId();

        if ($groupId)
        {
            return DataManager::retrieve_by_id(Group::class, (int) $groupId);
        }

        return null;
    }

    protected function getGroupButtonToolbarRenderer(Group $group): ButtonToolBarRenderer
    {
        $buttonToolbar = new ButtonToolBar();

        $courseManagementRights = CourseManagementRights::getInstance();

        $isAllowed = $courseManagementRights->is_allowed_for_platform_group(
            CourseManagementRights::TEACHER_DIRECT_SUBSCRIBE_RIGHT, $group->getId(), $this->get_course_id()
        );

        if (!in_array($group->getId(), $this->subscribedGroups) && $isAllowed)
        {
            $buttonToolbar->addItem(
                new Button(
                    $this->getTranslator()->trans('SubscribeGroup', [], Manager::CONTEXT), null, $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_SUBSCRIBE_GROUPS,
                        self::PARAM_OBJECTS => $group->getId()
                    ]
                ), ToolbarItem::DISPLAY_ICON_AND_LABEL, null, ['btn-success']
                )
            );
        }

        return new ButtonToolBarRenderer($buttonToolbar);
    }

    /**
     * @throws \ReflectionException
     */
    protected function getGroupId(): string
    {
        if (!$this->groupId)
        {
            $this->groupId = $this->getRequest()->query->get(
                \Chamilo\Application\Weblcms\Manager::PARAM_GROUP, $this->getRootGroup()->getId()
            );
        }

        return $this->groupId;
    }

    protected function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return $this->getService(GroupsTreeTraverser::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \ReflectionException
     */
    public function getRootGroup(): ?Group
    {
        if (!$this->rootGroup)
        {
            $this->rootGroup = DataManager::retrieve(
                Group::class, new DataClassRetrieveParameters(
                    new EqualityCondition(
                        new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_PARENT_ID),
                        new StaticConditionVariable(0)
                    )
                )
            );
        }

        return $this->rootGroup;
    }

    /**
     * @throws \QuickformException
     * @throws \ReflectionException
     */
    public function getUnsubscribedGroupCondition(): AndCondition
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_PARENT_ID),
            new StaticConditionVariable($this->getGroupId())
        );

        // filter already subscribed groups
        if ($this->subscribedGroups)
        {
            $conditions[] = new NotCondition(
                new InCondition(
                    new PropertyConditionVariable(Group::class, DataClass::PROPERTY_ID), $this->subscribedGroups
                )
            );
        }

        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        if (isset($query) && $query != '')
        {
            $conditions2[] = new ContainsCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME), $query
            );
            $conditions2[] = new ContainsCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_DESCRIPTION), $query
            );
            $conditions[] = new OrCondition($conditions2);
        }

        return new AndCondition($conditions);
    }

    public function getUnsubscribedGroupTableRenderer(): UnsubscribedGroupTableRenderer
    {
        return $this->getService(UnsubscribedGroupTableRenderer::class);
    }

    /**
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderCurrentGroup(): string
    {
        $html = [];

        $html[] = '<div class="col-sm-10">';
        $html[] = $this->renderGroupDetails();
        $html[] = $this->renderGroupSubgroupTable();

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \ReflectionException
     * @throws \QuickformException
     * @throws \Exception
     */
    protected function renderGroupDetails(): string
    {
        $translator = $this->getTranslator();
        $groupsTreeTraverser = $this->getGroupsTreeTraverser();

        $group = $this->getCurrentGroup();

        $html = [];

        $html[] = '<div class="panel panel-default">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">' . $group->get_name() . '</h3>';
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';
        $html[] = '<div class="row">';
        $html[] = '<div class="col-sm-12">';
        $html[] = '<b>' . $translator->trans('Code', [], Manager::CONTEXT) . ':</b> ' . $group->get_code();
        $html[] = '<br /><b>' . $translator->trans('Description', [], Manager::CONTEXT) . ':</b> ' .
            $groupsTreeTraverser->getFullyQualifiedNameForGroup($group);
        $html[] = '<br /><b>' . $translator->trans('NumberOfUsers', [], Manager::CONTEXT) . ':</b> ' .
            $groupsTreeTraverser->countUsersForGroup($group);
        $html[] = '<br /><b>' . $translator->trans('NumberOfSubgroups', [], Manager::CONTEXT) . ':</b> ' .
            $groupsTreeTraverser->countSubGroupsForGroup($group, true);
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="row">';
        $html[] = '<div class="col-sm-12">';
        $html[] = '<div style="margin-top: 20px;">';
        $html[] = $this->getGroupButtonToolbarRenderer($group)->render();
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \ReflectionException
     */
    protected function renderGroupMenu(): string
    {
        $tree = new PlatformgroupMenuRenderer($this, [$this->getRootGroup()->getId()]);

        $html = [];
        $html[] = '<div class="col-sm-2">';
        $html[] = $tree->render_as_tree();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \TableException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderGroupSubgroupTable(): string
    {
        $html = [];
        $html[] = '<div class="panel panel-default">';
        $html[] = '<div class="panel-heading">';
        $html[] =
            '<h3 class="panel-title">' . $this->getTranslator()->trans('Subgroups', [], Manager::CONTEXT) . '</h3>';
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';
        $html[] = $this->renderTable();
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    protected function renderInformationMessage(): string
    {
        $html = [];

        $html[] = '<div class="row">';
        $html[] = '<div class="col-sm-12">';

        $html[] = '<div class="alert alert-info">';
        $html[] = $this->getTranslator()->trans('SubscribeGroupsInformationMessage', [], Manager::CONTEXT);
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Exception
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = $this->getGroupService()->countGroups($this->getUnsubscribedGroupCondition());
        $unsubscribedGroupTableRenderer = $this->getUnsubscribedGroupTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $unsubscribedGroupTableRenderer->getParameterNames(),
            $unsubscribedGroupTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $groups = $this->getGroupService()->findGroups(
            $this->getUnsubscribedGroupCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $unsubscribedGroupTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $unsubscribedGroupTableRenderer->legacyRender($this, $tableParameterValues, $groups);
    }
}
