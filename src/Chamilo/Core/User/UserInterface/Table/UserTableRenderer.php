<?php
namespace Chamilo\Core\User\UserInterface\Table;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
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
    public const string TABLE_IDENTIFIER = Manager::PARAM_USER_ID;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer,
        PageNavigationCalculator $pager, DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory,
        protected UserUrlGenerator $userUrlGenerator, ClassnameUtilities $classnameUtilities,
        protected MiniButtonToolBarRenderer $miniButtonToolBarRenderer, protected ?User $currentUser = null
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

        $deleteUrl = $this->urlGenerator->fromParameters(
            [
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::DELETE->value
            ]
        );

        $actions->addAction(
            new TableAction(
                $deleteUrl, $this->translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            )
        );

        $activateUrl = $this->urlGenerator->fromParameters(
            [
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::ACTIVE->value,
                Manager::PARAM_ACTIVE => 1
            ]
        );

        $actions->addAction(
            new TableAction(
                $activateUrl, $this->translator->trans('ActivateSelected', [], StringUtilities::LIBRARIES), false
            )
        );

        $deactivateUrl = $this->urlGenerator->fromParameters(
            [
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::ACTIVE->value,
                Manager::PARAM_ACTIVE => 0
            ]
        );

        $actions->addAction(
            new TableAction(
                $deactivateUrl, $this->translator->trans('DeactivateSelected', [], StringUtilities::LIBRARIES)
            )
        );

        $resetPasswordUrl = $this->urlGenerator->fromParameters(
            [
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::RESET_PASSWORD_MULTI->value
            ]
        );

        $actions->addAction(
            new TableAction(
                $resetPasswordUrl, $this->translator->trans('ResetPassword')
            )
        );

        return $actions;
    }

    protected function initializeColumns(): void
    {
        $this->addColumn(
            $this->dataClassPropertyTableColumnFactory->getColumn(User::class, User::PROPERTY_OFFICIAL_CODE)
        );
        $this->addColumn(
            $this->dataClassPropertyTableColumnFactory->getColumn(User::class, User::PROPERTY_SURNAME)
        );
        $this->addColumn(
            $this->dataClassPropertyTableColumnFactory->getColumn(User::class, User::PROPERTY_GIVEN_NAME)
        );
        $this->addColumn(
            $this->dataClassPropertyTableColumnFactory->getColumn(User::class, User::PROPERTY_USERNAME)
        );
        $this->addColumn($this->dataClassPropertyTableColumnFactory->getColumn(User::class, User::PROPERTY_EMAIL));
        $this->addColumn(
            $this->dataClassPropertyTableColumnFactory->getColumn(
                User::class, User::PROPERTY_PLATFORM_ADMINISTRATOR
            )
        );
        $this->addColumn(
            $this->dataClassPropertyTableColumnFactory->getColumn(User::class, User::PROPERTY_ACTIVE)
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
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, mixed $result): string
    {
        $isPlatformAdministrator = $this->currentUser instanceof User && $this->currentUser->isPlatformAdministrator();

        $buttonToolBar = new MiniButtonToolBar();

        if ($isPlatformAdministrator) {
            $editUrl = $this->userUrlGenerator->getUpdateUrl($result);

            $buttonToolBar->addButton(
                new Button(
                    label: $this->translator->trans('Edit', [], StringUtilities::LIBRARIES),
                    inlineGlyph: new FontAwesomeGlyph('pencil-alt'), action: $editUrl, display: DisplayTypeEnum::ICON,
                    classes: ['btn-link']
                )
            );

            $detailUrl = $this->userUrlGenerator->getDetailUrl($result);

            $buttonToolBar->addButton(
                new Button(
                    label: $this->translator->trans('Detail', [], Manager::CONTEXT), inlineGlyph: new FontAwesomeGlyph(
                    'info-circle'
                ), action: $detailUrl, display: DisplayTypeEnum::ICON, classes: ['btn-link']
                )
            );
        }

        if ($this->currentUser instanceof User && $result->getId() != $this->currentUser->getId()) {
            if ($isPlatformAdministrator) {
                $deleteUrl = $this->userUrlGenerator->getDeleteUrl($result);

                $buttonToolBar->addButton(
                    new Button(
                        label: $this->translator->trans('Delete', [], StringUtilities::LIBRARIES),
                        inlineGlyph: new FontAwesomeGlyph('times'), action: $deleteUrl, display: DisplayTypeEnum::ICON,
                        confirmationMessage: $this->translator->trans(
                            'ConfirmChosenAction', [], StringUtilities::LIBRARIES
                        ), classes: ['btn-link']
                    )
                );

                $changeUserUrl = $this->userUrlGenerator->getChangeUserUrl($result);

                $buttonToolBar->addButton(
                    new Button(
                        label: $this->translator->trans('LoginAsUser', [], Manager::CONTEXT),
                        inlineGlyph: new FontAwesomeGlyph('mask'), action: $changeUserUrl,
                        display: DisplayTypeEnum::ICON, classes: ['btn-link']
                    )
                );
            }
            else {
                $buttonToolBar->addButton(
                    new Button(
                        label: $this->translator->trans('DeleteNA', [], StringUtilities::LIBRARIES),
                        inlineGlyph: new FontAwesomeGlyph('times', ['text-muted']), display: DisplayTypeEnum::ICON,
                        classes: ['btn-link']
                    )
                );
            }
        }
        else {
            $buttonToolBar->addButton(
                new Button(
                    label: $this->translator->trans('DeleteNA', [], StringUtilities::LIBRARIES),
                    inlineGlyph: new FontAwesomeGlyph('times', ['text-muted']), display: DisplayTypeEnum::ICON,
                    classes: ['btn-link']
                )
            );
        }

        return $this->miniButtonToolBarRenderer->render($buttonToolBar);
    }
}
