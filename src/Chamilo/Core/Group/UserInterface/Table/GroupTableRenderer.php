<?php
namespace Chamilo\Core\Group\UserInterface\Table;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Service\GroupUrlGenerator;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
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
    public const string COLUMN_SUBGROUPS = 'Subgroups';
    public const string COLUMN_USERS = 'Users';
    public const string TABLE_IDENTIFIER = DataClass::PROPERTY_ID;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer,
        PageNavigationCalculator $pager, DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory,
        ClassnameUtilities $classnameUtilities, protected GroupMembershipService $groupMembershipService,
        protected GroupUrlGenerator $groupUrlGenerator, protected GroupsTreeTraverser $groupsTreeTraverser,
        protected MiniButtonToolBarRenderer $miniButtonToolBarRenderer, protected StringUtilities $stringUtilities
    )
    {
        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory,
            $classnameUtilities
        );
    }

    public function getTableActions(): TableActions
    {
        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $removeUrl = $this->urlGenerator->fromParameters([
            ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
            ApplicationInterface::PARAM_ACTION => ActionEnum::DELETE->value
        ]);

        $actions->addAction(
            new TableAction(
                $removeUrl, $this->translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            )
        );

        $truncateUrl = $this->urlGenerator->fromParameters([
            ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
            ApplicationInterface::PARAM_ACTION => ActionEnum::TRUNCATE->value
        ]);

        $actions->addAction(
            new TableAction(
                $truncateUrl, $this->translator->trans('TruncateSelected', [], Manager::CONTEXT)
            )
        );

        return $actions;
    }

    protected function initializeColumns(): void
    {
        $this->addColumn(
            $this->dataClassPropertyTableColumnFactory->getColumn(Group::class, Group::PROPERTY_NAME)
        );
        $this->addColumn(
            $this->dataClassPropertyTableColumnFactory->getColumn(Group::class, Group::PROPERTY_CODE)
        );
        $this->addColumn(
            $this->dataClassPropertyTableColumnFactory->getColumn(Group::class, Group::PROPERTY_DESCRIPTION)
        );
        $this->addColumn(
            new StaticTableColumn(
                self::COLUMN_USERS,
                $this->translator->trans(self::COLUMN_USERS, [], \Chamilo\Core\User\Manager::CONTEXT)
            )
        );
        $this->addColumn(
            new StaticTableColumn(
                self::COLUMN_SUBGROUPS,
                $this->translator->trans(self::COLUMN_SUBGROUPS, [], \Chamilo\Core\User\Manager::CONTEXT)
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
        switch ($column->getName()) {
            case Group::PROPERTY_NAME :
                $title = parent::renderCell($column, $resultPosition, $result);
                $shortTitle = $title;

                if (strlen($shortTitle) > 53) {
                    $shortTitle = mb_substr($shortTitle, 0, 50) . '&hellip;';
                }

                $viewUrl = $this->urlGenerator->fromParameters(
                    [
                        ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                        ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value,
                        DataClass::PROPERTY_ID => $result->getId()
                    ]
                );

                return '<a href="' . htmlentities($viewUrl) . '" title="' . $title . '">' . $shortTitle . '</a>';
            case Group::PROPERTY_DESCRIPTION :
                $description = strip_tags(parent::renderCell($column, $resultPosition, $result));

                if (strlen($description) > 175) {
                    $description = mb_substr($description, 0, 170) . '&hellip;';
                }

                return $this->stringUtilities->truncate($description);
            case $this->translator->trans(self::COLUMN_USERS, [], \Chamilo\Core\User\Manager::CONTEXT) :
                return (string) $this->groupMembershipService->countUsersByGroup($result);
            case $this->translator->trans(self::COLUMN_SUBGROUPS, [], \Chamilo\Core\User\Manager::CONTEXT) :
                return (string) $this->groupsTreeTraverser->countSubGroupsForGroup($result, true);
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
        $buttonToolBar = new MiniButtonToolBar();

        $editUrl = $this->groupUrlGenerator->getUpdateUrl($result);

        $buttonToolBar->addButton(
            new Button(
                label: $this->translator->trans('Edit', [], StringUtilities::LIBRARIES),
                inlineGlyph: new FontAwesomeGlyph(
                    'pencil-alt'
                ), action: $editUrl, display: DisplayTypeEnum::ICON, classes: ['btn-link']
            )
        );

        $subscribeUrl = $this->groupUrlGenerator->getSubscribeUrl($result);

        $buttonToolBar->addButton(
            new Button(
                label: $this->translator->trans('AddUsers', [], Manager::CONTEXT), inlineGlyph: new FontAwesomeGlyph(
                'plus-circle'
            ), action: $subscribeUrl, display: DisplayTypeEnum::ICON, classes: ['btn-link']
            )
        );

        $visible = ($this->groupMembershipService->countSubscribedUsersByGroupIdentifier($result->getId()) > 0);

        if ($visible) {
            $truncateUrl = $this->groupUrlGenerator->getTruncateUrl($result);

            $buttonToolBar->addButton(
                new Button(
                    label: $this->translator->trans('Truncate', [], Manager::CONTEXT),
                    inlineGlyph: new FontAwesomeGlyph(
                        'trash-alt'
                    ), action: $truncateUrl, display: DisplayTypeEnum::ICON,
                    confirmationMessage: $this->translator->trans(
                        'ConfirmChosenAction', [], StringUtilities::LIBRARIES
                    ), classes: ['btn-link']
                )
            );
        }
        else {
            $buttonToolBar->addButton(
                new Button(
                    label: $this->translator->trans('TruncateNA', [], Manager::CONTEXT),
                    inlineGlyph: new FontAwesomeGlyph(
                        'trash-alt', ['text-muted']
                    ), display: DisplayTypeEnum::ICON, classes: ['btn-link']
                )
            );
        }

        $deleteUrl = $this->groupUrlGenerator->getDeleteUrl($result);

        $buttonToolBar->addButton(
            new Button(
                label: $this->translator->trans('Delete', [], StringUtilities::LIBRARIES),
                inlineGlyph: new FontAwesomeGlyph(
                    'times'
                ), action: $deleteUrl, display: DisplayTypeEnum::ICON, confirmationMessage: $this->translator->trans(
                    'ConfirmChosenAction', [], StringUtilities::LIBRARIES
                ), classes: ['btn-link']
            )
        );

        $moveUrl = $this->groupUrlGenerator->getMoveUrl($result);

        $buttonToolBar->addButton(
            new Button(
                label: $this->translator->trans('Move', [], StringUtilities::LIBRARIES),
                inlineGlyph: new FontAwesomeGlyph(
                    'window-restore', ['fa-flip-horizontal'], null, 'fas'
                ), action: $moveUrl, display: DisplayTypeEnum::ICON, classes: ['btn-link']
            )
        );

        return $this->miniButtonToolBarRenderer->render($buttonToolBar);
    }
}
