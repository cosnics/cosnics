<?php
namespace Chamilo\Core\Group\UserInterface\Table;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
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
class NonSubscribedUserTableRenderer extends DataClassListTableRenderer
    implements TableRowActionsSupport, TableActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_USER_ID;

    protected MiniButtonToolBarRenderer $miniButtonToolBarRenderer;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer,
        PageNavigationCalculator $pager, DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory,
        ClassnameUtilities $classnameUtilities, MiniButtonToolBarRenderer $miniButtonToolBarRenderer
    )
    {
        $this->miniButtonToolBarRenderer = $miniButtonToolBarRenderer;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory,
            $classnameUtilities
        );
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

        $unsubscribeUrl = $urlGenerator->fromRequest([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Application::PARAM_ACTION => Manager::ACTION_SUBSCRIBE
        ]);

        $actions->addAction(
            new TableAction(
                $unsubscribeUrl, $translator->trans('SubscribeSelected', [], Manager::CONTEXT), false
            )
        );

        return $actions;
    }

    protected function initializeColumns(): void
    {
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_SURNAME)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_GIVEN_NAME)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_USERNAME)
        );

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_EMAIL)
        );

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                User::class, User::PROPERTY_PLATFORM_ADMINISTRATOR
            )
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $result
     *
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     * @throws \QuickformException
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, mixed $result): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $buttonToolBar = new MiniButtonToolBar();

        $subscribeUrl = $urlGenerator->fromRequest([
            Application::PARAM_ACTION => Manager::ACTION_SUBSCRIBE,
            Manager::PARAM_USER_ID => $result->getId()

        ]);

        $buttonToolBar->addButton(
            new Button(
                $translator->trans('UnsubscribeSelected', [], Manager::CONTEXT), new FontAwesomeGlyph('plus-circle'),
                $subscribeUrl, DisplayTypeEnum::ICON, classes: ['btn-link']
            )
        );

        return $this->getMiniButtonToolBarRenderer()->render($buttonToolBar);
    }
}
