<?php
namespace Chamilo\Core\Group\UserInterface\Table;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupUrlGenerator;
use Chamilo\Core\Group\Storage\DataClass\SubscribedUser;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\MiniButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\MiniButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
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
class SubscribedUserTableRenderer extends DataClassListTableRenderer
    implements TableRowActionsSupport, TableActionsSupport
{
    public const string TABLE_IDENTIFIER = Manager::PARAM_RELATION_ID;

    protected GroupUrlGenerator $groupUrlGenerator;

    protected MiniButtonToolBarRenderer $miniButtonToolBarRenderer;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer,
        PageNavigationCalculator $pager, DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory,
        GroupUrlGenerator $groupUrlGenerator, ClassnameUtilities $classnameUtilities,
        MiniButtonToolBarRenderer $miniButtonToolBarRenderer
    )
    {
        $this->groupUrlGenerator = $groupUrlGenerator;
        $this->miniButtonToolBarRenderer = $miniButtonToolBarRenderer;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory,
            $classnameUtilities
        );
    }

    public function getGroupUrlGenerator(): GroupUrlGenerator
    {
        return $this->groupUrlGenerator;
    }

    public function getMiniButtonToolBarRenderer(): MiniButtonToolBarRenderer
    {
        return $this->miniButtonToolBarRenderer;
    }

    public function getTableActions(): TableActions
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $unsubscribeUrl = $urlGenerator->fromParameters([
            ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
            ApplicationInterface::PARAM_ACTION => ActionEnum::UNSUBSCRIBE->value
        ]);

        $actions->addAction(
            new TableAction(
                $unsubscribeUrl, $translator->trans('UnsubscribeSelected', [], Manager::CONTEXT), false
            )
        );

        return $actions;
    }

    protected function initializeColumns(): void
    {
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(SubscribedUser::class, User::PROPERTY_GIVEN_NAME)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(SubscribedUser::class, User::PROPERTY_SURNAME)
        );
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\SubscribedUser $result
     *
     * @throws \Chamilo\Libraries\Protocol\Error\Architecture\Exception\NoSuchClassException
     * @throws \QuickformException
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, mixed $result): string
    {
        $translator = $this->getTranslator();

        $buttonToolBar = new MiniButtonToolBar();

        $unsubscribeUrl = $this->getGroupUrlGenerator()->getUnsubscribeUserUrl($result);

        $buttonToolBar->addButton(
            new Button(
                label: $translator->trans('UnsubscribeSelected', [], Manager::CONTEXT),
                inlineGlyph: new FontAwesomeGlyph('times'), action: $unsubscribeUrl, display: DisplayTypeEnum::ICON,
                classes: ['btn-link']
            )
        );

        return $this->getMiniButtonToolBarRenderer()->render($buttonToolBar);
    }
}
