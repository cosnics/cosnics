<?php
namespace Chamilo\Core\Group\Table;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Group\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const COLUMN_SUBGROUPS = 'Subgroups';
    public const COLUMN_USERS = 'Users';

    public const TABLE_IDENTIFIER = Manager::PARAM_GROUP_ID;

    protected GroupMembershipService $groupMembershipService;

    protected GroupsTreeTraverser $groupsTreeTraverser;

    protected StringUtilities $stringUtilities;

    public function __construct(
        GroupsTreeTraverser $groupsTreeTraverser, GroupMembershipService $groupMembershipService,
        StringUtilities $stringUtilities, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager
    )
    {
        $this->stringUtilities = $stringUtilities;
        $this->groupsTreeTraverser = $groupsTreeTraverser;
        $this->groupMembershipService = $groupMembershipService;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    public function getGroupMembershipService(): GroupMembershipService
    {
        return $this->groupMembershipService;
    }

    public function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return $this->groupsTreeTraverser;
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    /**
     * @throws \ReflectionException
     */
    public function getTableActions(): TableActions
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $removeUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::package(),
            Application::PARAM_ACTION => Manager::ACTION_TRUNCATE_GROUP
        ]);

        $actions->addAction(
            new TableAction(
                $removeUrl, $translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            )
        );

        $truncateUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::package(),
            Application::PARAM_ACTION => Manager::ACTION_TRUNCATE_GROUP
        ]);

        $actions->addAction(
            new TableAction(
                $truncateUrl, $translator->trans('TruncateSelected', [], 'Chamilo\Core\Group')
            )
        );

        return $actions;
    }

    protected function initializeColumns()
    {
        $translator = $this->getTranslator();

        $this->addColumn(new DataClassPropertyTableColumn(Group::class, Group::PROPERTY_NAME));
        $this->addColumn(new DataClassPropertyTableColumn(Group::class, Group::PROPERTY_CODE));
        $this->addColumn(new DataClassPropertyTableColumn(Group::class, Group::PROPERTY_DESCRIPTION));
        $this->addColumn(
            new StaticTableColumn(
                self::COLUMN_USERS, $translator->trans(self::COLUMN_USERS, [], 'Chamilo\Core\User\Manager')
            )
        );
        $this->addColumn(
            new StaticTableColumn(
                self::COLUMN_SUBGROUPS, $translator->trans(self::COLUMN_SUBGROUPS, [], 'Chamilo\Core\User\Manager')
            )
        );
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @throws \Exception
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $group): string
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();
        $stringUtilities = $this->getStringUtilities();
        $groupsTreeTraverser = $this->getGroupsTreeTraverser();

        switch ($column->get_name())
        {
            case Group::PROPERTY_NAME :
                $title = parent::renderCell($column, $group);
                $title_short = $title;

                if (strlen($title_short) > 53)
                {
                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
                }

                $viewUrl = $urlGenerator->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => Manager::ACTION_VIEW_GROUP,
                        Manager::PARAM_GROUP_ID => $group->getId()
                    ]
                );

                return '<a href="' . htmlentities($viewUrl) . '" title="' . $title . '">' . $title_short . '</a>';
            case Group::PROPERTY_DESCRIPTION :
                $description = strip_tags(parent::renderCell($column, $group));

                if (strlen($description) > 175)
                {
                    $description = mb_substr($description, 0, 170) . '&hellip;';
                }

                return $stringUtilities->truncate($description);
            case $translator->trans(self::COLUMN_USERS, [], 'Chamilo\Core\User\Manager') :
                return (string) $groupsTreeTraverser->countUsersForGroup($group);
            case $translator->trans(self::COLUMN_SUBGROUPS, [], 'Chamilo\Core\User\Manager') :
                return (string) $groupsTreeTraverser->countSubGroupsForGroup($group, true);
        }

        return parent::renderCell($column, $resultPosition, $group);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $group): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();
        $groupMembershipService = $this->getGroupMembershipService();

        $toolbar = new Toolbar();

        $editUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::package(),
            Application::PARAM_ACTION => Manager::ACTION_EDIT_GROUP,
            Manager::PARAM_GROUP_ID => $group->getId()
        ]);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $editUrl, ToolbarItem::DISPLAY_ICON
            )
        );

        $subscribeUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::package(),
            Application::PARAM_ACTION => Manager::ACTION_SUBSCRIBE_USER_BROWSER,
            Manager::PARAM_GROUP_ID => $group->getId()
        ]);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('AddUsers', [], 'Chamilo\Core\Group'), new FontAwesomeGlyph('plus-circle'),
                $subscribeUrl, ToolbarItem::DISPLAY_ICON
            )
        );

        $visible = ($groupMembershipService->countSubscribedUsersForGroupIdentifier($group->getId()) > 0);

        if ($visible)
        {
            $truncateUrl = $urlGenerator->fromParameters([
                Application::PARAM_CONTEXT => Manager::package(),
                Application::PARAM_ACTION => Manager::ACTION_TRUNCATE_GROUP,
                Manager::PARAM_GROUP_ID => $group->getId()
            ]);

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Truncate', [], 'Chamilo\Core\Group'), new FontAwesomeGlyph('trash-alt'),
                    $truncateUrl, ToolbarItem::DISPLAY_ICON, true
                )
            );
        }
        else
        {

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('TruncateNA', [], 'Chamilo\Core\Group'),
                    new FontAwesomeGlyph('trash-alt', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        $deleteUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::package(),
            Application::PARAM_ACTION => Manager::ACTION_DELETE_GROUP,
            Manager::PARAM_GROUP_ID => $group->getId()
        ]);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'), $deleteUrl,
                ToolbarItem::DISPLAY_ICON, true
            )
        );

        $moveUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::package(),
            Application::PARAM_ACTION => Manager::ACTION_MOVE_GROUP,
            Manager::PARAM_GROUP_ID => $group->getId()
        ]);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Move', [], StringUtilities::LIBRARIES),
                new FontAwesomeGlyph('window-restore', ['fa-flip-horizontal'], null, 'fas'), $moveUrl,
                ToolbarItem::DISPLAY_ICON
            )
        );

        return $toolbar->render();
    }
}
