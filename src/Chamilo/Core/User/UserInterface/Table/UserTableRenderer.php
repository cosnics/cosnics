<?php
namespace Chamilo\Core\User\UserInterface\Table;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
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
 * @package Chamilo\Core\User\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_USER_ID;

    protected MiniButtonToolBarRenderer $miniButtonToolBarRenderer;

    protected User $user;

    protected UserUrlGenerator $userUrlGenerator;

    public function __construct(
        User $user, Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer,
        PageNavigationCalculator $pager, DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory,
        UserUrlGenerator $userUrlGenerator, ClassnameUtilities $classnameUtilities,
        MiniButtonToolBarRenderer $miniButtonToolBarRenderer
    )
    {
        $this->user = $user;
        $this->userUrlGenerator = $userUrlGenerator;
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
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $deleteUrl = $urlGenerator->fromParameters(
            [Application::PARAM_CONTEXT => Manager::CONTEXT, Application::PARAM_ACTION => Manager::ACTION_DELETE]
        );

        $actions->addAction(
            new TableAction(
                $deleteUrl, $translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            )
        );

        $activateUrl = $urlGenerator->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_ACTIVE,
                Manager::PARAM_ACTIVE => 1
            ]
        );

        $actions->addAction(
            new TableAction(
                $activateUrl, $translator->trans('ActivateSelected', [], StringUtilities::LIBRARIES), false
            )
        );

        $deactivateUrl = $urlGenerator->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_ACTIVE,
                Manager::PARAM_ACTIVE => 0
            ]
        );

        $actions->addAction(
            new TableAction(
                $deactivateUrl, $translator->trans('DeactivateSelected', [], StringUtilities::LIBRARIES)
            )
        );

        $resetPasswordUrl = $urlGenerator->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_RESET_PASSWORD_MULTI
            ]
        );

        $actions->addAction(
            new TableAction(
                $resetPasswordUrl, $translator->trans('ResetPassword')
            )
        );

        return $actions;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserUrlGenerator(): UserUrlGenerator
    {
        return $this->userUrlGenerator;
    }

    protected function initializeColumns(): void
    {
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_OFFICIAL_CODE)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_SURNAME)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_GIVEN_NAME)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_USERNAME)
        );
        $this->addColumn($this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_EMAIL));
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                User::class, User::PROPERTY_PLATFORM_ADMINISTRATOR
            )
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_ACTIVE)
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $result
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, mixed $result): string
    {
        $trueGlyph = new FontAwesomeGlyph('circle', ['text-success']);
        $falseGlyph = new FontAwesomeGlyph('circle', ['text-danger']);

        // Add special features here
        switch ($column->getName()) {
            case User::PROPERTY_PLATFORM_ADMINISTRATOR :
                return $result->getPlatformAdmin() ? $trueGlyph->render() : $falseGlyph->render();
            case User::PROPERTY_ACTIVE :
                return $result->getActive() ? $trueGlyph->render() : $falseGlyph->render();
        }

        return parent::renderCell($column, $resultPosition, $result);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $result
     *
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     * @throws \QuickformException
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, mixed $result): string
    {
        $translator = $this->getTranslator();

        $buttonToolBar = new MiniButtonToolBar();

        if ($this->getUser()->isPlatformAdministrator()) {
            $editUrl = $this->getUserUrlGenerator()->getUpdateUrl($result);

            $buttonToolBar->addButton(
                new Button(
                    label: $translator->trans('Edit', [], StringUtilities::LIBRARIES),
                    inlineGlyph: new FontAwesomeGlyph('pencil-alt'), action: $editUrl, display: DisplayTypeEnum::ICON,
                    classes: ['btn-link']
                )
            );

            $detailUrl = $this->getUserUrlGenerator()->getDetailUrl($result);

            $buttonToolBar->addButton(
                new Button(
                    label: $translator->trans('Detail', [], Manager::CONTEXT), inlineGlyph: new FontAwesomeGlyph(
                    'info-circle'
                ), action: $detailUrl, display: DisplayTypeEnum::ICON, classes: ['btn-link']
                )
            );
        }

        if ($result->getId() != $this->getUser()->getId()) {
            if ($this->getUser()->isPlatformAdministrator()) {
                $deleteUrl = $this->getUserUrlGenerator()->getDeleteUrl($result);

                $buttonToolBar->addButton(
                    new Button(
                        label: $translator->trans('Delete', [], StringUtilities::LIBRARIES),
                        inlineGlyph: new FontAwesomeGlyph('times'), action: $deleteUrl, display: DisplayTypeEnum::ICON,
                        confirmationMessage: $this->getTranslator()->trans(
                            'ConfirmChosenAction', [], StringUtilities::LIBRARIES
                        ), classes: ['btn-link']
                    )
                );
            }
            else {
                $buttonToolBar->addButton(
                    new Button(
                        label: $translator->trans('DeleteNA', [], StringUtilities::LIBRARIES),
                        inlineGlyph: new FontAwesomeGlyph('times', ['text-muted']), display: DisplayTypeEnum::ICON,
                        classes: ['btn-link']
                    )
                );
            }

            if ($this->getUser()->isPlatformAdministrator()) {
                $changeUserUrl = $this->getUserUrlGenerator()->getChangeUserUrl($result);

                $buttonToolBar->addButton(
                    new Button(
                        label: $translator->trans('LoginAsUser', [], Manager::CONTEXT),
                        inlineGlyph: new FontAwesomeGlyph('mask'), action: $changeUserUrl,
                        display: DisplayTypeEnum::ICON, classes: ['btn-link']
                    )
                );
            }
        }
        else {
            $buttonToolBar->addButton(
                new Button(
                    label: $translator->trans('DeleteNA', [], StringUtilities::LIBRARIES),
                    inlineGlyph: new FontAwesomeGlyph('times', ['text-muted']), display: DisplayTypeEnum::ICON,
                    classes: ['btn-link']
                )
            );
        }

        return $this->getMiniButtonToolBarRenderer()->render($buttonToolBar);
    }
}
