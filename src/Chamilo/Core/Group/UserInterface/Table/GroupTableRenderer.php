<?php
namespace Chamilo\Core\Group\UserInterface\Table;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Service\GroupUrlGenerator;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\Toolbar;
use Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\ToolbarItem;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\StaticTableColumn;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\FormAction\TableAction;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\FormAction\TableActions;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableResultPosition;
use Chamilo\Libraries\UserInterface\Table\Architecture\Interface\TableActionsSupport;
use Chamilo\Libraries\UserInterface\Table\Architecture\Interface\TableRowActionsSupport;
use Chamilo\Libraries\UserInterface\Table\Factory\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\UserInterface\Table\Service\DataClassListTableRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\ListHtmlTableRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\Pager;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Group\UserInterface\Table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const COLUMN_SUBGROUPS = 'Subgroups';
    public const COLUMN_USERS = 'Users';

    public const TABLE_IDENTIFIER = Manager::PARAM_GROUP_ID;

    protected GroupMembershipService $groupMembershipService;

    protected GroupUrlGenerator $groupUrlGenerator;

    protected GroupsTreeTraverser $groupsTreeTraverser;

    protected StringUtilities $stringUtilities;

    public function __construct(
        GroupsTreeTraverser $groupsTreeTraverser, GroupMembershipService $groupMembershipService,
        StringUtilities $stringUtilities, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory, GroupUrlGenerator $groupUrlGenerator,
        ClassnameUtilities $classnameUtilities
    )
    {
        $this->stringUtilities = $stringUtilities;
        $this->groupsTreeTraverser = $groupsTreeTraverser;
        $this->groupMembershipService = $groupMembershipService;
        $this->groupUrlGenerator = $groupUrlGenerator;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory,
            $classnameUtilities
        );
    }

    public function getGroupMembershipService(): GroupMembershipService
    {
        return $this->groupMembershipService;
    }

    public function getGroupUrlGenerator(): GroupUrlGenerator
    {
        return $this->groupUrlGenerator;
    }

    public function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return $this->groupsTreeTraverser;
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function getTableActions(): TableActions
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $removeUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Application::PARAM_ACTION => Manager::ACTION_TRUNCATE
        ]);

        $actions->addAction(
            new TableAction(
                $removeUrl, $translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            )
        );

        $truncateUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Application::PARAM_ACTION => Manager::ACTION_TRUNCATE
        ]);

        $actions->addAction(
            new TableAction(
                $truncateUrl, $translator->trans('TruncateSelected', [], 'Chamilo\Core\Group')
            )
        );

        return $actions;
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
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $result
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, mixed $result): string
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();
        $stringUtilities = $this->getStringUtilities();
        $groupsTreeTraverser = $this->getGroupsTreeTraverser();

        switch ($column->getName())
        {
            case Group::PROPERTY_NAME :
                $title = parent::renderCell($column, $resultPosition, $result);
                $title_short = $title;

                if (strlen($title_short) > 53)
                {
                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
                }

                $viewUrl = $urlGenerator->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => Manager::ACTION_VIEW,
                        Manager::PARAM_GROUP_ID => $result->getId()
                    ]
                );

                return '<a href="' . htmlentities($viewUrl) . '" title="' . $title . '">' . $title_short . '</a>';
            case Group::PROPERTY_DESCRIPTION :
                $description = strip_tags(parent::renderCell($column, $resultPosition, $result));

                if (strlen($description) > 175)
                {
                    $description = mb_substr($description, 0, 170) . '&hellip;';
                }

                return $stringUtilities->truncate($description);
            case $translator->trans(self::COLUMN_USERS, [], 'Chamilo\Core\User\Manager') :
                return (string) $groupsTreeTraverser->countUsersForGroup($result);
            case $translator->trans(self::COLUMN_SUBGROUPS, [], 'Chamilo\Core\User\Manager') :
                return (string) $groupsTreeTraverser->countSubGroupsForGroup($result, true);
        }

        return parent::renderCell($column, $resultPosition, $result);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $result
     *
     * @throws \Exception
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, mixed $result): string
    {
        $translator = $this->getTranslator();
        $groupMembershipService = $this->getGroupMembershipService();
        $groupUrlGenerator = $this->getGroupUrlGenerator();

        $toolbar = new Toolbar();

        $editUrl = $groupUrlGenerator->getUpdateUrl($result);

        $toolbar->addItem(
            new ToolbarItem(
                $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $editUrl, ToolbarItem::DISPLAY_ICON
            )
        );

        $subscribeUrl = $groupUrlGenerator->getSubscribeUrl($result);

        $toolbar->addItem(
            new ToolbarItem(
                $translator->trans('AddUsers', [], 'Chamilo\Core\Group'), new FontAwesomeGlyph('plus-circle'),
                $subscribeUrl, ToolbarItem::DISPLAY_ICON
            )
        );

        $visible = ($groupMembershipService->countSubscribedUsersForGroupIdentifier($result->getId()) > 0);

        if ($visible)
        {
            $truncateUrl = $groupUrlGenerator->getTruncateUrl($result);

            $toolbar->addItem(
                new ToolbarItem(
                    label: $translator->trans('Truncate', [], 'Chamilo\Core\Group'), image: new FontAwesomeGlyph(
                    'trash-alt'
                ), href: $truncateUrl, display: ToolbarItem::DISPLAY_ICON, confirmation: true,
                    confirmationMessage: $this->getTranslator()->trans(
                        'ConfirmChosenAction', [], StringUtilities::LIBRARIES
                    )
                )
            );
        }
        else
        {

            $toolbar->addItem(
                new ToolbarItem(
                    $translator->trans('TruncateNA', [], 'Chamilo\Core\Group'),
                    new FontAwesomeGlyph('trash-alt', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        $deleteUrl = $groupUrlGenerator->getDeleteUrl($result);

        $toolbar->addItem(
            new ToolbarItem(
                label: $translator->trans('Delete', [], StringUtilities::LIBRARIES), image: new FontAwesomeGlyph(
                'times'
            ), href: $deleteUrl, display: ToolbarItem::DISPLAY_ICON, confirmation: true,
                confirmationMessage: $this->getTranslator()->trans(
                    'ConfirmChosenAction', [], StringUtilities::LIBRARIES
                )
            )
        );

        $moveUrl = $groupUrlGenerator->getMoveUrl($result);

        $toolbar->addItem(
            new ToolbarItem(
                $translator->trans('Move', [], StringUtilities::LIBRARIES),
                new FontAwesomeGlyph('window-restore', ['fa-flip-horizontal'], null, 'fas'), $moveUrl,
                ToolbarItem::DISPLAY_ICON
            )
        );

        return $toolbar->render();
    }
}
