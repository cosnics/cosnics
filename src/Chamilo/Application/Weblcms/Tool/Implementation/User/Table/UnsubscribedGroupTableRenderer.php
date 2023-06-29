<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Table;

use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Component\GroupSubscribeComponent;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Application\Weblcms\Tool\Manager as ToolManager;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableParameterValues;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\User\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UnsubscribedGroupTableRenderer extends DataClassListTableRenderer
    implements TableRowActionsSupport, TableActionsSupport
{
    public const PROPERTY_SUBGROUPS = 'Subgroups';

    public const PROPERTY_USERS = 'Users';

    public const TABLE_IDENTIFIER = Manager::PARAM_OBJECTS;

    /**
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     */
    protected Application $application;

    protected GroupsTreeTraverser $groupsTreeTraverser;

    protected User $user;

    public function __construct(
        GroupsTreeTraverser $groupsTreeTraverser, User $user, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->groupsTreeTraverser = $groupsTreeTraverser;
        $this->user = $user;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
    }

    public function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return $this->groupsTreeTraverser;
    }

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        if ($this->application->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $actions->addAction(
                new TableAction(
                    $urlGenerator->fromRequest(
                        [
                            ToolManager::PARAM_ACTION => Manager::ACTION_SUBSCRIBE_GROUPS
                        ]
                    ), $translator->trans('SubscribeSelectedGroups', [], Manager::CONTEXT), false
                )
            );
        }

        return $actions;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    protected function initializeColumns(): void
    {
        $translator = $this->getTranslator();

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(Group::class, Group::PROPERTY_NAME)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(Group::class, Group::PROPERTY_CODE)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(Group::class, Group::PROPERTY_DESCRIPTION)
        );
        $this->addColumn(
            new StaticTableColumn(
                self::PROPERTY_USERS, $translator->trans(self::PROPERTY_USERS, [], \Chamilo\Core\User\Manager::CONTEXT)
            )
        );
        $this->addColumn(
            new StaticTableColumn(
                self::PROPERTY_SUBGROUPS, $translator->trans(self::PROPERTY_SUBGROUPS, [], Manager::CONTEXT)
            )
        );
    }

    /**
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     */
    public function legacyRender(
        Application $application, TableParameterValues $parameterValues, ArrayCollection $tableData,
        ?string $tableName = null
    ): string
    {
        $this->application = $application;

        return parent::render($parameterValues, $tableData, $tableName);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @throws \Exception
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $group): string
    {
        $urlGenerator = $this->getUrlGenerator();

        switch ($column->get_name())
        {
            case Group::PROPERTY_NAME :
                $title = parent:: renderCell($column, $resultPosition, $group);

                $url = $urlGenerator->fromRequest(
                    [
                        ToolManager::PARAM_ACTION => Manager::ACTION_SUBSCRIBE_GROUP_DETAILS,
                        Manager::PARAM_GROUP => $group->getId()
                    ]
                );

                return '<a href="' . $url . '">' . $title . '</a>';

            case Group::PROPERTY_DESCRIPTION :
                return $this->getGroupsTreeTraverser()->getFullyQualifiedNameForGroup($group);
            case self::PROPERTY_USERS:
                return (string) $this->getGroupsTreeTraverser()->countUsersForGroup($group);
            case self::PROPERTY_SUBGROUPS:
                return (string) $this->getGroupsTreeTraverser()->countSubGroupsForGroup($group);
        }

        return parent::renderCell($column, $resultPosition, $group);
    }

    /**
     * @throws \ReflectionException
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $groupWithSubscriptionStatus): string
    {
        if ($this->application->isGroupSubscribed($groupWithSubscriptionStatus->getId()))
        {
            return '';
        }

        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        if ($this->getUser()->isPlatformAdmin() || ($this->application->is_allowed(
                    WeblcmsRights::EDIT_RIGHT
                ) && CourseManagementRights:: getInstance()->is_allowed_for_platform_group(
                    CourseManagementRights::TEACHER_DIRECT_SUBSCRIBE_RIGHT, $groupWithSubscriptionStatus->get_id(),
                    $this->application->get_course_id()
                )))
        {

            $subscribe_group_users = $this->application->get_course()->get_course_setting(
                'allow_subscribe_users_from_group', $this->application->get_tool_id()
            );

            if ($subscribe_group_users)
            {
                // subscribe users of group
                $parameters[ToolManager::PARAM_ACTION] = Manager::ACTION_SUBSCRIBE_USERS_FROM_GROUP;
                $parameters[Manager::PARAM_OBJECTS] = $groupWithSubscriptionStatus->get_id();

                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('SubscribeUsersFromGroup', [], Manager::CONTEXT),
                        new FontAwesomeGlyph('copy'), $urlGenerator->fromRequest($parameters), ToolbarItem::DISPLAY_ICON
                    )
                );
            }

            // subscribe group
            $parameters[ToolManager::PARAM_ACTION] = Manager::ACTION_SUBSCRIBE_GROUPS;
            $parameters[Manager::PARAM_OBJECTS] = $groupWithSubscriptionStatus->get_id();
            $parameters[GroupSubscribeComponent::PARAM_RETURN_TO_COMPONENT] = $this->application->get_action();

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('SubscribeGroup', [], Manager::CONTEXT), new FontAwesomeGlyph('plus-circle'),
                    $urlGenerator->fromRequest($parameters), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }
}
