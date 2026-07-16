<?php
namespace Chamilo\Core\Group\UserInterface\Table;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupUrlGenerator;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\MiniButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\MiniButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
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
class GroupMembershipTableRenderer extends DataClassListTableRenderer
    implements TableRowActionsSupport, TableActionsSupport
{
    public const string TABLE_IDENTIFIER = DataClass::PROPERTY_ID;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer,
        PageNavigationCalculator $pager, DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory,
        ClassnameUtilities $classnameUtilities, protected readonly GroupUrlGenerator $groupUrlGenerator,
        protected readonly MiniButtonToolBarRenderer $miniButtonToolBarRenderer
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

        $unsubscribeUrl = $this->urlGenerator->fromParameters([
            ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
            ApplicationInterface::PARAM_ACTION => ActionEnum::UNSUBSCRIBE->value
        ]);

        $actions->addAction(
            new TableAction(
                $unsubscribeUrl, $this->translator->trans('UnsubscribeSelected', [], Manager::CONTEXT), false
            )
        );

        return $actions;
    }

    protected function initializeColumns(): void
    {
        $this->addColumn(
            $this->dataClassPropertyTableColumnFactory->getColumn(User::class, User::PROPERTY_GIVEN_NAME)
        );
        $this->addColumn(
            $this->dataClassPropertyTableColumnFactory->getColumn(User::class, User::PROPERTY_SURNAME)
        );
    }

    /**
     * @param \Chamilo\Core\Group\Storage\Entity\GroupMembership $result
     *
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, mixed $result): string
    {
        return match ($column->getName()) {
            User::PROPERTY_GIVEN_NAME => (string) $result->getUser()->getGivenName(),
            User::PROPERTY_SURNAME => (string) $result->getUser()->getSurname(),
            default => parent::renderCell($column, $resultPosition, $result),
        };
    }

    /**
     * @param \Chamilo\Core\Group\Storage\Entity\GroupMembership $result
     *
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, mixed $result): string
    {
        $buttonToolBar = new MiniButtonToolBar();

        $unsubscribeUrl = $this->groupUrlGenerator->getUnsubscribeUserUrl($result);

        $buttonToolBar->addButton(
            new Button(
                label: $this->translator->trans('UnsubscribeSelected', [], Manager::CONTEXT),
                inlineGlyph: new FontAwesomeGlyph('times'), action: $unsubscribeUrl, display: DisplayTypeEnum::ICON,
                classes: ['btn-link']
            )
        );

        return $this->miniButtonToolBarRenderer->render($buttonToolBar);
    }
}
