<?php
namespace Chamilo\Core\Group\UserInterface\Table;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Service\GroupUrlGenerator;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\MiniButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\MiniButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\StaticTableColumn;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableAction\TableAction;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableAction\TableActions;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableResultPosition;
use Chamilo\Libraries\UserInterface\Table\Architecture\Interface\TableActionsSupport;
use Chamilo\Libraries\UserInterface\Table\Architecture\Interface\TableRowActionsSupport;
use Chamilo\Libraries\UserInterface\Table\Factory\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\UserInterface\Table\Service\DataClassListTableRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\ListHtmlTableRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\PageNavigationCalculator;
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

    protected MiniButtonToolBarRenderer $miniButtonToolBarRenderer;

    protected StringUtilities $stringUtilities;

    public function __construct(
        GroupsTreeTraverser $groupsTreeTraverser, GroupMembershipService $groupMembershipService,
        StringUtilities $stringUtilities, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, PageNavigationCalculator $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory, GroupUrlGenerator $groupUrlGenerator,
        ClassnameUtilities $classnameUtilities, MiniButtonToolBarRenderer $miniButtonToolBarRenderer
    )
    {
        $this->stringUtilities = $stringUtilities;
        $this->groupsTreeTraverser = $groupsTreeTraverser;
        $this->groupMembershipService = $groupMembershipService;
        $this->groupUrlGenerator = $groupUrlGenerator;
        $this->miniButtonToolBarRenderer = $miniButtonToolBarRenderer;

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

    public function getMiniButtonToolBarRenderer(): MiniButtonToolBarRenderer
    {
        return $this->miniButtonToolBarRenderer;
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
            Application::PARAM_ACTION => ActionEnum::DELETE->value
        ]);

        $actions->addAction(
            new TableAction(
                $removeUrl, $translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            )
        );

        $truncateUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Application::PARAM_ACTION => ActionEnum::TRUNCATE->value
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

        switch ($column->getName()) {
            case Group::PROPERTY_NAME :
                $title = parent::renderCell($column, $resultPosition, $result);
                $shortTitle = $title;

                if (strlen($shortTitle) > 53) {
                    $shortTitle = mb_substr($shortTitle, 0, 50) . '&hellip;';
                }

                $viewUrl = $urlGenerator->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => ActionEnum::BROWSE->value,
                        Manager::PARAM_GROUP_ID => $result->getId()
                    ]
                );

                return '<a href="' . htmlentities($viewUrl) . '" title="' . $title . '">' . $shortTitle . '</a>';
            case Group::PROPERTY_DESCRIPTION :
                $description = strip_tags(parent::renderCell($column, $resultPosition, $result));

                if (strlen($description) > 175) {
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

        $buttonToolBar = new MiniButtonToolBar();

        $editUrl = $groupUrlGenerator->getUpdateUrl($result);

        $buttonToolBar->addButton(
            new Button(
                label: $translator->trans('Edit', [], StringUtilities::LIBRARIES), inlineGlyph: new FontAwesomeGlyph(
                'pencil-alt'
            ), action: $editUrl, display: DisplayTypeEnum::ICON, classes: ['btn-link']
            )
        );

        $subscribeUrl = $groupUrlGenerator->getSubscribeUrl($result);

        $buttonToolBar->addButton(
            new Button(
                label: $translator->trans('AddUsers', [], 'Chamilo\Core\Group'), inlineGlyph: new FontAwesomeGlyph(
                'plus-circle'
            ), action: $subscribeUrl, display: DisplayTypeEnum::ICON, classes: ['btn-link']
            )
        );

        $visible = ($groupMembershipService->countSubscribedUsersForGroupIdentifier($result->getId()) > 0);

        if ($visible) {
            $truncateUrl = $groupUrlGenerator->getTruncateUrl($result);

            $buttonToolBar->addButton(
                new Button(
                    label: $translator->trans('Truncate', [], 'Chamilo\Core\Group'), inlineGlyph: new FontAwesomeGlyph(
                    'trash-alt'
                ), action: $truncateUrl, display: DisplayTypeEnum::ICON, confirmationMessage: $this->getTranslator()
                    ->trans(
                        'ConfirmChosenAction', [], StringUtilities::LIBRARIES
                    ), classes: ['btn-link']
                )
            );
        }
        else {
            $buttonToolBar->addButton(
                new Button(
                    label: $translator->trans('TruncateNA', [], 'Chamilo\Core\Group'),
                    inlineGlyph: new FontAwesomeGlyph('trash-alt', ['text-muted']), display: DisplayTypeEnum::ICON,
                    classes: ['btn-link']
                )
            );
        }

        $deleteUrl = $groupUrlGenerator->getDeleteUrl($result);

        $buttonToolBar->addButton(
            new Button(
                label: $translator->trans('Delete', [], StringUtilities::LIBRARIES), inlineGlyph: new FontAwesomeGlyph(
                'times'
            ), action: $deleteUrl, display: DisplayTypeEnum::ICON, confirmationMessage: $this->getTranslator()->trans(
                'ConfirmChosenAction', [], StringUtilities::LIBRARIES
            ), classes: ['btn-link']
            )
        );

        $moveUrl = $groupUrlGenerator->getMoveUrl($result);

        $buttonToolBar->addButton(
            new Button(
                label: $translator->trans('Move', [], StringUtilities::LIBRARIES), inlineGlyph: new FontAwesomeGlyph(
                'window-restore', ['fa-flip-horizontal'], null, 'fas'
            ), action: $moveUrl, display: DisplayTypeEnum::ICON, classes: ['btn-link']
            )
        );

        return $this->getMiniButtonToolBarRenderer()->render($buttonToolBar);
    }
}
