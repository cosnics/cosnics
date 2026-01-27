<?php
namespace Chamilo\Core\User\UserInterface\Table;

use Chamilo\Core\Admin\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\Toolbar;
use Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\ToolbarItem;
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
 * @package Chamilo\Core\User\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_USER_ID;

    protected ConfigurationConsulter $configurationConsulter;

    protected User $user;

    protected UserUrlGenerator $userUrlGenerator;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, User $user, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory, UserUrlGenerator $userUrlGenerator,
        ClassnameUtilities $classnameUtilities
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->user = $user;
        $this->userUrlGenerator = $userUrlGenerator;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory,
            $classnameUtilities
        );
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
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
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_STATUS)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_PLATFORM_ADMINISTRATOR)
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
        $translator = $this->getTranslator();

        $trueGlyph = new FontAwesomeGlyph('circle', ['text-success']);
        $falseGlyph = new FontAwesomeGlyph('circle', ['text-danger']);

        // Add special features here
        switch ($column->getName())
        {
            // Exceptions that need post-processing go here ...
            case User::PROPERTY_STATUS :
                if ($result->getStatus() == '1')
                {
                    return $translator->trans('CourseAdmin', [], Manager::CONTEXT);
                }
                else
                {
                    return $translator->trans('Student', [], Manager::CONTEXT);
                }
            case User::PROPERTY_PLATFORM_ADMINISTRATOR :
                return $result->getPlatformAdmin() ? $trueGlyph->render() : $falseGlyph->render();
            case User::PROPERTY_ACTIVE :
                return $result->getActive() ? $trueGlyph->render() : $falseGlyph->render();
        }

        return parent::renderCell($column, $resultPosition, $result);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $result
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, mixed $result): string
    {
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        if ($this->getUser()->isPlatformAdministrator())
        {
            $editUrl = $this->getUserUrlGenerator()->getUpdateUrl($result);

            $toolbar->addItem(
                new ToolbarItem(
                    $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                    $editUrl, ToolbarItem::DISPLAY_ICON
                )
            );

            $detailUrl = $this->getUserUrlGenerator()->getDetailUrl($result);

            $toolbar->addItem(
                new ToolBarItem(
                    $translator->trans('Detail', [], Manager::CONTEXT), new FontAwesomeGlyph('info-circle'), $detailUrl,
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($result->getId() != $this->getUser()->getId())
        {
            if ($this->getUser()->isPlatformAdministrator())
            {
                $deleteUrl = $this->getUserUrlGenerator()->getDeleteUrl($result);

                $toolbar->addItem(
                    new ToolBarItem(
                        label: $translator->trans('Delete', [], StringUtilities::LIBRARIES),
                        image: new FontAwesomeGlyph('times'), href: $deleteUrl, display: ToolbarItem::DISPLAY_ICON,
                        confirmation: true, confirmationMessage: $this->getTranslator()->trans(
                        'ConfirmChosenAction', [], StringUtilities::LIBRARIES
                    )
                    )
                );
            }
            else
            {
                $toolbar->addItem(
                    new ToolBarItem(
                        $translator->trans('DeleteNA', [], StringUtilities::LIBRARIES),
                        new FontAwesomeGlyph('times', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                    )
                );
            }

            if ($this->getUser()->isPlatformAdministrator())
            {
                $changeUserUrl = $this->getUserUrlGenerator()->getChangeUserUrl($result);

                $toolbar->addItem(
                    new ToolBarItem(
                        $translator->trans('LoginAsUser', [], Manager::CONTEXT), new FontAwesomeGlyph('mask'),
                        $changeUserUrl, ToolbarItem::DISPLAY_ICON
                    )
                );
            }
        }
        else
        {
            $toolbar->addItem(
                new ToolBarItem(
                    $translator->trans('DeleteNA', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('times', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }
}
