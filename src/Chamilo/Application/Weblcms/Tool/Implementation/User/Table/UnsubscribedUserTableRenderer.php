<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Table;

use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableParameterValues;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\User\Table
 * @author  Stijn Van Hoecke
 * @author  Sven Vanpoucke - Hogeschool Gent - Refactoring from ObjectTable to RecordTable
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UnsubscribedUserTableRenderer extends DataClassListTableRenderer
    implements TableRowActionsSupport, TableActionsSupport
{
    public const DEFAULT_ORDER_COLUMN_INDEX = 1;

    public const TABLE_IDENTIFIER = Manager::PARAM_OBJECTS;

    /**
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     */
    protected Application $application;

    protected ChamiloRequest $chamiloRequest;

    protected ConfigurationConsulter $configurationConsulter;

    protected User $user;

    public function __construct(
        ChamiloRequest $chamiloRequest, ConfigurationConsulter $configurationConsulter, User $user,
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager
    )
    {
        $this->user = $user;
        $this->configurationConsulter = $configurationConsulter;
        $this->chamiloRequest = $chamiloRequest;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    public function getChamiloRequest(): ChamiloRequest
    {
        return $this->chamiloRequest;
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getTableActions(): TableActions
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        if (!$this->getChamiloRequest()->query->get(\Chamilo\Application\Weblcms\Manager::PARAM_GROUP))
        {
            $actions->addAction(
                new TableAction(
                    $urlGenerator->fromRequest(
                        [
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_SUBSCRIBE
                        ]
                    ), $translator->trans('SubscribeSelectedAsStudent', [], Manager::CONTEXT), false
                )
            );

            // make teacher
            $actions->addAction(
                new TableAction(
                    $urlGenerator->fromRequest(
                        [
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_SUBSCRIBE_AS_ADMIN
                        ]
                    ), $translator->trans('SubscribeSelectedAsAdmin', [], Manager::CONTEXT), false
                )
            );

            // make student
            $actions->addAction(
                new TableAction(
                    $urlGenerator->fromRequest(
                        [
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_REQUEST_SUBSCRIBE_USERS
                        ]
                    ), $translator->trans('RequestUsers', [], Manager::CONTEXT), false
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
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_USERNAME));

        $showEmail = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\User', 'show_email_addresses']);

        if ($showEmail)
        {
            $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_EMAIL));
        }

        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_STATUS));
    }

    /**
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     */
    public function legacyRender(
        Application $application, TableParameterValues $parameterValues, ArrayCollection $tableData,
        ?string $tableName = null
    ): string
    {
        $this->application = $application;

        return parent::render($parameterValues, $tableData, $tableName);
    }

    protected function renderCell(
        TableColumn $column, TableResultPosition $resultPosition, $userWithSubscriptionStatus
    ): string
    {
        $translator = $this->getTranslator();

        switch ($column->get_name())
        {
            case User::PROPERTY_STATUS :
                switch ($userWithSubscriptionStatus->get_status())
                {
                    case CourseEntityRelation::STATUS_TEACHER :
                        return $translator->trans('CourseAdmin', [], Manager::CONTEXT);
                    case CourseEntityRelation::STATUS_STUDENT :
                        return $translator->trans('Student', [], Manager::CONTEXT);
                    default :
                        return $translator->trans('Unknown', [], Manager::CONTEXT);
                }
            case User::PROPERTY_PLATFORMADMIN :
                if ($userWithSubscriptionStatus[User::PROPERTY_PLATFORMADMIN] == '1')
                {
                    return $translator->trans('PlatformAdministrator', [], Manager::CONTEXT);
                }
                else
                {
                    return '';
                }
            case User::PROPERTY_EMAIL :
                $email_url = 'mailto:' . $userWithSubscriptionStatus->get_email();

                return '<a href="' . $email_url . '">' . $userWithSubscriptionStatus->get_email() . '</a>';
        }

        return parent::renderCell($column, $resultPosition, $userWithSubscriptionStatus);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $userWithSubscriptionStatus): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        if ($this->getUser()->is_platform_admin() || ($this->application->is_allowed(
                    WeblcmsRights::EDIT_RIGHT
                ) && CourseManagementRights::getInstance()->is_allowed_management(
                    CourseManagementRights::TEACHER_DIRECT_SUBSCRIBE_RIGHT, $this->application->get_course_id(),
                    WeblcmsRights::TYPE_COURSE, $userWithSubscriptionStatus->get_id()
                )))

        {
            // subscribe regular student
            $parameters = [];
            $parameters[Manager::PARAM_OBJECTS] = $userWithSubscriptionStatus->get_id();
            $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = Manager::ACTION_SUBSCRIBE;
            $subscribe_url = $urlGenerator->fromRequest($parameters);

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('SubscribeAsStudent', [], Manager::CONTEXT),
                    new FontAwesomeGlyph('user-graduate'), $subscribe_url, ToolbarItem::DISPLAY_ICON
                )
            );

            // subscribe as course admin
            $parameters = [];
            $parameters[Manager::PARAM_OBJECTS] = $userWithSubscriptionStatus->get_id();
            $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = Manager::ACTION_SUBSCRIBE_AS_ADMIN;
            $subscribe_url = $urlGenerator->fromRequest($parameters);

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('SubscribeAsTeacher', [], Manager::CONTEXT), new FontAwesomeGlyph('user-tie'),
                    $subscribe_url, ToolbarItem::DISPLAY_ICON
                )
            );
        }
        elseif ($this->getUser()->is_platform_admin() || ($this->application->is_allowed(
                    WeblcmsRights::EDIT_RIGHT
                ) && CourseManagementRights::getInstance()->is_allowed_management(
                    CourseManagementRights::TEACHER_REQUEST_SUBSCRIBE_RIGHT, $this->application->get_course_id(),
                    WeblcmsRights::TYPE_COURSE, $userWithSubscriptionStatus->get_id()
                )))

        {
            if (!DataManager::is_user_requested_for_course(
                $userWithSubscriptionStatus->get_id(), $this->application->get_course_id()
            ))
            {
                $parameters = [];
                $parameters[Manager::PARAM_OBJECTS] = $userWithSubscriptionStatus->get_id();
                $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] =
                    Manager::ACTION_REQUEST_SUBSCRIBE_USER;
                $subscribe_request_url = $urlGenerator->fromRequest($parameters);

                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('RequestUser', [], Manager::CONTEXT),
                        new FontAwesomeGlyph('user-clock', [], null, 'fas'), $subscribe_request_url,
                        ToolbarItem::DISPLAY_ICON
                    )
                );
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('UserRequestPending', [], Manager::CONTEXT), new FontAwesomeGlyph('clock'),
                        null, ToolbarItem::DISPLAY_ICON
                    )
                );
            }
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('SubscribeNA', [], Manager::CONTEXT),
                    new FontAwesomeGlyph('plus-circle', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }

}