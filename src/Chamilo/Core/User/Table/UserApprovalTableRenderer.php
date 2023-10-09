<?php
namespace Chamilo\Core\User\Table;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
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
class UserApprovalTableRenderer extends DataClassListTableRenderer
    implements TableRowActionsSupport, TableActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_USER_USER_ID;

    protected ConfigurationConsulter $configurationConsulter;

    protected User $user;

    protected UserUrlGenerator $userUrlGenerator;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, User $user, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory, UserUrlGenerator $userUrlGenerator
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->user = $user;
        $this->userUrlGenerator = $userUrlGenerator;

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

        $approveUrl = $urlGenerator->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_APPROVE_USER,
                Manager::PARAM_CHOICE => Manager::CHOICE_APPROVE
            ]
        );

        $actions->addAction(
            new TableAction(
                $approveUrl, $translator->trans('ApproveSelected', [], Manager::CONTEXT)
            )
        );

        $denyUrl = $urlGenerator->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_APPROVE_USER,
                Manager::PARAM_CHOICE => Manager::CHOICE_DENY
            ]
        );

        $actions->addAction(
            new TableAction(
                $denyUrl, $translator->trans('DenySelected', [], Manager::CONTEXT)
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
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_USERNAME)
        );
        $this->addColumn($this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_EMAIL));
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_LASTNAME)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_FIRSTNAME)
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $user): string
    {
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        if ($this->getUser()->isPlatformAdmin())
        {
            $approveUrl = $this->getUserUrlGenerator()->getApproveUrl($user);

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Approve', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('check-circle'),
                    $approveUrl, ToolbarItem::DISPLAY_ICON
                )
            );

            $denyUrl = $this->getUserUrlGenerator()->getDenyUrl($user);

            $toolbar->add_item(
                new ToolBarItem(
                    $translator->trans('Deny', [], Manager::CONTEXT), new FontAwesomeGlyph('times-circle'), $denyUrl,
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }
}
