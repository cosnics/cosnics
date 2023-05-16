<?php
namespace Chamilo\Core\User\Table;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Template\LoginTemplate;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
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
 * @package Chamilo\Core\User\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AdminUserTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_USER_USER_ID;

    protected ConfigurationConsulter $configurationConsulter;

    protected User $user;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, User $user, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->user = $user;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
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
            [Application::PARAM_CONTEXT => Manager::CONTEXT, Manager::PARAM_ACTION => Manager::ACTION_DELETE_USER]
        );

        $actions->addAction(
            new TableAction(
                $deleteUrl, $translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            )
        );

        $activateUrl = $urlGenerator->fromParameters(
            [Application::PARAM_CONTEXT => Manager::CONTEXT, Manager::PARAM_ACTION => Manager::ACTION_ACTIVATE]
        );

        $actions->addAction(
            new TableAction(
                $activateUrl, $translator->trans('ActivateSelected', [], StringUtilities::LIBRARIES), false
            )
        );

        $deactivateUrl = $urlGenerator->fromParameters(
            [Application::PARAM_CONTEXT => Manager::CONTEXT, Manager::PARAM_ACTION => Manager::ACTION_DEACTIVATE]
        );

        $actions->addAction(
            new TableAction(
                $deactivateUrl, $translator->trans('DeactivateSelected', [], StringUtilities::LIBRARIES)
            )
        );

        $resetPasswordUrl = $urlGenerator->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Manager::PARAM_ACTION => Manager::ACTION_RESET_PASSWORD_MULTI
            ]
        );

        $actions->addAction(
            new TableAction(
                $resetPasswordUrl, $translator->trans('ResetPassword')
            )
        );

        if ($this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'active_online_email_editor']))
        {
            $emailUrl = $urlGenerator->fromParameters(
                [Application::PARAM_CONTEXT => Manager::CONTEXT, Manager::PARAM_ACTION => Manager::ACTION_EMAIL]
            );

            $actions->addAction(
                new TableAction(
                    $emailUrl, $translator->trans('EmailSelected', [], Manager::CONTEXT), false
                )
            );
        }

        return $actions;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    protected function initializeColumns()
    {
        $this->addColumn($this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_OFFICIAL_CODE));
        $this->addColumn($this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_LASTNAME));
        $this->addColumn($this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_FIRSTNAME));
        $this->addColumn($this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_USERNAME));
        $this->addColumn($this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_EMAIL));
        $this->addColumn($this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_STATUS));
        $this->addColumn($this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_PLATFORMADMIN));
        $this->addColumn($this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_ACTIVE));
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $user): string
    {
        $translator = $this->getTranslator();

        $trueGlyph = new FontAwesomeGlyph('circle', ['text-success']);
        $falseGlyph = new FontAwesomeGlyph('circle', ['text-danger']);

        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case User::PROPERTY_STATUS :
                if ($user->get_status() == '1')
                {
                    return $translator->trans('CourseAdmin', [], Manager::CONTEXT);
                }
                else
                {
                    return $translator->trans('Student', [], Manager::CONTEXT);
                }
            case User::PROPERTY_PLATFORMADMIN :
                return $user->get_platformadmin() ? $trueGlyph->render() : $falseGlyph->render();
            case User::PROPERTY_ACTIVE :
                return $user->get_active() ? $trueGlyph->render() : $falseGlyph->render();
        }

        return parent::renderCell($column, $resultPosition, $user);
    }

    /**
     * @throws \ReflectionException
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $user): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        if ($this->getUser()->is_platform_admin())
        {
            $editUrl = $urlGenerator->fromParameters(
                [
                    Application::PARAM_CONTEXT,
                    Manager::PARAM_ACTION => Manager::ACTION_UPDATE_USER,
                    Manager::PARAM_USER_USER_ID => $user->getId()
                ]
            );

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                    $editUrl, ToolbarItem::DISPLAY_ICON
                )
            );

            $detailUrl = $urlGenerator->fromParameters(
                [
                    Application::PARAM_CONTEXT,
                    Manager::PARAM_ACTION => Manager::ACTION_USER_DETAIL,
                    Manager::PARAM_USER_USER_ID => $user->getId()
                ]
            );

            $toolbar->add_item(
                new ToolBarItem(
                    $translator->trans('Detail', [], Manager::CONTEXT), new FontAwesomeGlyph('info-circle'), $detailUrl,
                    ToolbarItem::DISPLAY_ICON
                )
            );

            $reportUrl = $urlGenerator->fromParameters(
                [
                    Application::PARAM_CONTEXT,
                    Manager::PARAM_ACTION => Manager::ACTION_REPORTING,
                    \Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Manager::PARAM_ACTION => \Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Manager::ACTION_VIEW,
                    \Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Manager::PARAM_TEMPLATE_ID => LoginTemplate::TEMPLATE_ID,
                    Manager::PARAM_USER_USER_ID => $user->get_id()
                ]
            );

            $toolbar->add_item(
                new ToolBarItem(
                    $translator->trans('Report', [], Manager::CONTEXT), new FontAwesomeGlyph('chart-pie'), $reportUrl,
                    ToolbarItem::DISPLAY_ICON
                )
            );

            $viewQuotaUrl = $urlGenerator->fromParameters(
                [
                    Application::PARAM_CONTEXT,
                    Manager::PARAM_ACTION => Manager::ACTION_VIEW_QUOTA,
                    Manager::PARAM_USER_USER_ID => $user->getId()
                ]
            );

            $toolbar->add_item(
                new ToolBarItem(
                    $translator->trans('ViewQuota', [], Manager::CONTEXT), new FontAwesomeGlyph('folder'),
                    $viewQuotaUrl, ToolbarItem::DISPLAY_ICON
                )
            );

            if ($this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'active_online_email_editor']))
            {
                $emailUrl = $urlGenerator->fromParameters(
                    [
                        Application::PARAM_CONTEXT,
                        Manager::PARAM_ACTION => Manager::ACTION_EMAIL,
                        Manager::PARAM_USER_USER_ID => $user->getId()
                    ]
                );

                $toolbar->add_item(
                    new ToolBarItem(
                        $translator->trans('SendEmail', [], Manager::CONTEXT), new FontAwesomeGlyph('envelope'),
                        $emailUrl, ToolbarItem::DISPLAY_ICON
                    )
                );
            }
        }

        if ($user->get_id() != $this->getUser()->getId())
        {
            if ($this->getUser()->is_platform_admin())
            {
                $deleteUrl = $urlGenerator->fromParameters(
                    [
                        Application::PARAM_CONTEXT,
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE_USER,
                        Manager::PARAM_USER_USER_ID => $user->getId()
                    ]
                );

                $toolbar->add_item(
                    new ToolBarItem(
                        $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                        $deleteUrl, ToolbarItem::DISPLAY_ICON, true
                    )
                );
            }
            else
            {
                $toolbar->add_item(
                    new ToolBarItem(
                        $translator->trans('DeleteNA', [], StringUtilities::LIBRARIES),
                        new FontAwesomeGlyph('times', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                    )
                );
            }

            if ($this->getUser()->is_platform_admin())
            {
                $changeUserUrl = $urlGenerator->fromParameters(
                    [
                        Application::PARAM_CONTEXT,
                        Manager::PARAM_ACTION => Manager::ACTION_CHANGE_USER,
                        Manager::PARAM_USER_USER_ID => $user->getId()
                    ]
                );

                $toolbar->add_item(
                    new ToolBarItem(
                        $translator->trans('LoginAsUser', [], Manager::CONTEXT), new FontAwesomeGlyph('mask'),
                        $changeUserUrl, ToolbarItem::DISPLAY_ICON
                    )
                );
            }
        }
        else
        {
            $toolbar->add_item(
                new ToolBarItem(
                    $translator->trans('DeleteNA', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('times', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }
}
