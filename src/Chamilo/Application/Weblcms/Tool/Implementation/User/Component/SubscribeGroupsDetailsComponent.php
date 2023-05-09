<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Table\GroupUsersTableRenderer;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SubscribeGroupsDetailsComponent extends SubscribeGroupsTabComponent
{

    protected function getGroupButtonToolbarRenderer(Group $group): ButtonToolBarRenderer
    {
        $buttonToolbar = new ButtonToolBar();

        $courseManagementRights = CourseManagementRights::getInstance();

        $isAllowed = $courseManagementRights->is_allowed_for_platform_group(
            CourseManagementRights::TEACHER_DIRECT_SUBSCRIBE_RIGHT, $group->getId(), $this->get_course_id()
        );

        if (!$this->isGroupSubscribed($group->getId()) && ($this->getUser()->is_platform_admin() || $isAllowed))
        {
            $buttonToolbar->addItem(
                new Button(
                    $this->getTranslation('SubscribeGroup'), null, $this->get_url(
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
     * @throws \Exception
     */
    public function getGroupUsersCondition(): AndCondition
    {
        $group = $this->getCurrentGroup();
        $subscribedUserIds = $this->getGroupsTreeTraverser()->findUserIdentifiersForGroup($group);

        $conditions = [];

        $conditions[] = new InCondition(
            new PropertyConditionVariable(User::class, DataClass::PROPERTY_ID), $subscribedUserIds
        );

        $conditionProperties = [];
        $conditionProperties[] = new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME);
        $conditionProperties[] = new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME);
        $conditionProperties[] = new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME);
        $conditionProperties[] = new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL);

        $searchCondition = $this->tabButtonToolbarRenderer->getConditions($conditionProperties);

        if ($searchCondition)
        {
            $conditions[] = $searchCondition;
        }

        return new AndCondition($conditions);
    }

    public function getGroupUsersTableRenderer(): GroupUsersTableRenderer
    {
        return $this->getService(GroupUsersTableRenderer::class);
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
     * Renders the details for the currently selected group
     *
     * @throws \Exception
     */
    protected function renderGroupDetails(): string
    {
        $groupsTreeTraverser = $this->getGroupsTreeTraverser();
        $group = $this->getCurrentGroup();

        $html = [];

        $html[] = '<div class="row">';
        $html[] = '<div class="col-sm-12">';
        $html[] = '<b>' . $this->getTranslation('Code') . ':</b> ' . $group->get_code();
        $html[] = '<br /><b>' . $this->getTranslation('Description') . ':</b> ' .
            $groupsTreeTraverser->getFullyQualifiedNameForGroup($group);
        $html[] = '<br /><b>' . $this->getTranslation('NumberOfUsers') . ':</b> ' .
            $groupsTreeTraverser->countUsersForGroup($group);
        $html[] = '<br /><b>' . $this->getTranslation('NumberOfSubgroups') . ':</b> ' .
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

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Exception
     */
    protected function renderGroupUsersTable(): string
    {
        $html = [];
        $html[] = '<div class="tab-content-header">';
        $html[] = '<h5>' . $this->getTranslation('Users') . '</h5>';
        $html[] = '</div>';

        $html[] = '<div>' . $this->tabButtonToolbarRenderer->render() . '</div>';

        $totalNumberOfItems = $this->getUserService()->countUsers($this->getGroupUsersCondition());
        $groupUsersTableRenderer = $this->getGroupUsersTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $groupUsersTableRenderer->getParameterNames(), $groupUsersTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $users = $this->getUserService()->findUsers(
            $this->getGroupUsersCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $groupUsersTableRenderer->determineOrderBy($tableParameterValues)
        );

        $html[] = $groupUsersTableRenderer->render($tableParameterValues, $users);

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Exception
     */
    protected function renderTabContent(): string
    {
        $html = [];

        $html[] = $this->renderGroupDetails();
        $html[] = $this->renderGroupUsersTable();

        return implode(PHP_EOL, $html);
    }
}
