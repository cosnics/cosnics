<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Table;

use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Component\AllSubscribed\AllSubscribedUserTableColumnModel;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Interfaces\UserListActionsExtenderInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Application\Weblcms\Tool\Manager as ToolManager;
use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Column\SortableStaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordListTableRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableParameterValues;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\User\Table
 * @author  Stijn Van Hoecke
 * @author  Sven Vanpoucke - Hogeschool Gent - Refactoring from ObjectTable to RecordTable
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AllSubscribedUserTableRenderer extends RecordListTableRenderer implements TableRowActionsSupport
{
    public const DEFAULT_ORDER_COLUMN_INDEX = 1;

    public const PROPERTY_SUBSCRIPTION_STATUS = 'subscription_status';
    public const PROPERTY_SUBSCRIPTION_TYPE = 'subscription_type';

    /**
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     */
    protected Application $application;

    protected ConfigurationConsulter $configurationConsulter;

    protected RegistrationConsulter $registrationConsulter;

    protected User $user;

    public function __construct(
        RegistrationConsulter $registrationConsulter, ConfigurationConsulter $configurationConsulter, User $user,
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->user = $user;
        $this->configurationConsulter = $configurationConsulter;
        $this->registrationConsulter = $registrationConsulter;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Exception
     */
    protected function addAdditionalActions(Toolbar $toolbar, $currentUserId)
    {
        $integrationPackages = $this->getRegistrationConsulter()->getIntegrationRegistrations(Manager::CONTEXT);

        foreach ($integrationPackages as $integrationPackage)
        {
            $class = $integrationPackage['context'] . '\UserListActionsExtender';

            if (!class_exists($class))
            {
                throw new Exception(
                    sprintf(
                        'The given package %s does not have a UserListActionsExtender class',
                        $integrationPackage['context']
                    )
                );
            }

            $userListActionsExtender = new $class();

            if (!$userListActionsExtender instanceof UserListActionsExtenderInterface)
            {
                throw new Exception(
                    sprintf(
                        'The given package %s does not have a valid UserListActionsExtender class ' .
                        'that extends from UserListActionsExtenderInterface', $integrationPackage['context']
                    )
                );
            }

            $userListActionsExtender->getActions($toolbar, $currentUserId);
        }
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->registrationConsulter;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    protected function initializeColumns(): void
    {
        $translator = $this->getTranslator();

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_OFFICIAL_CODE)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_LASTNAME)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_FIRSTNAME)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_USERNAME)
        );

        $showEmail = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\User', 'show_email_addresses']);

        if ($showEmail)
        {
            $this->addColumn(
                $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_EMAIL)
            );
        }

        $this->addColumn(
            new SortableStaticTableColumn(
                self::PROPERTY_SUBSCRIPTION_STATUS, $translator->trans('SubscriptionStatus', [], Manager::CONTEXT)
            )
        );
        $this->addColumn(
            new StaticTableColumn(
                self::PROPERTY_SUBSCRIPTION_TYPE, $translator->trans('SubscriptionType', [], Manager::CONTEXT)
            )
        );
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
        TableColumn $column, TableResultPosition $resultPosition, $userWithSubscriptionStatusAndType
    ): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        switch ($column->get_name())
        {
            case self::PROPERTY_SUBSCRIPTION_TYPE :
                $type = $userWithSubscriptionStatusAndType[AllSubscribedUserTableColumnModel::SUBSCRIPTION_TYPE];
                switch ($type)
                {
                    case 1 :
                        return $translator->trans('SubscribedDireclty', [], Manager::CONTEXT);
                    case 2 :
                        return $translator->trans('SubscribedGroup', [], Manager::CONTEXT);
                    default :
                        return $translator->trans(
                            ($type % 2 == 0) ? 'SubscribedGroup' : 'SubscribedDirecltyAndGroup', [], Manager::CONTEXT
                        );
                }
            case self::PROPERTY_SUBSCRIPTION_STATUS :
                switch ($userWithSubscriptionStatusAndType[AllSubscribedUserTableColumnModel::SUBSCRIPTION_STATUS])
                {
                    case CourseEntityRelation::STATUS_TEACHER :
                        return $translator->trans('CourseAdmin', [], Manager::CONTEXT);
                    case CourseEntityRelation::STATUS_STUDENT :
                        return $translator->trans('Student', [], Manager::CONTEXT);
                    default :
                        return $translator->trans('Unknown', [], Manager::CONTEXT);
                }
            case User::PROPERTY_PLATFORMADMIN :
                if ($userWithSubscriptionStatusAndType[User::PROPERTY_PLATFORMADMIN] == '1')
                {
                    return $translator->trans('PlatformAdministrator', [], Manager::CONTEXT);
                }
                else
                {
                    return '';
                }
            case User::PROPERTY_EMAIL :
                $email = $userWithSubscriptionStatusAndType[User::PROPERTY_EMAIL];

                $activeOnlineEmailEditor = $this->getConfigurationConsulter()->getSetting(
                    ['Chamilo\Core\Admin', 'active_online_email_editor']
                );

                if ($activeOnlineEmailEditor)
                {
                    $parameters = [];
                    $parameters[ToolManager::PARAM_ACTION] = Manager::ACTION_EMAIL;
                    $parameters[Manager::PARAM_OBJECTS] = $userWithSubscriptionStatusAndType[DataClass::PROPERTY_ID];

                    $email_url = $urlGenerator->fromRequest($parameters);
                }
                else
                {
                    $email_url = 'mailto:' . $email;
                }

                return '<a href="' . $email_url . '">' . $email . '</a>';
        }

        return parent::renderCell($column, $resultPosition, $userWithSubscriptionStatusAndType);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $userWithSubscriptionStatusAndType
    ): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $userIdentifier = $userWithSubscriptionStatusAndType[DataClass::PROPERTY_ID];

        $parameters = [];
        $parameters[ToolManager::PARAM_ACTION] = Manager::ACTION_USER_DETAILS;
        $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_USERS] = $userIdentifier;

        $details_url = $urlGenerator->fromRequest($parameters);

        // always show details
        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Details', [], Manager::CONTEXT), new FontAwesomeGlyph('info-circle'), $details_url,
                ToolbarItem::DISPLAY_ICON
            )
        );

        // display the actions to change the individual status and unsubscribe
        // if:
        // (1) the user has edit rights
        // AND
        // (2) the row is not the current user
        // AND
        // (3) the row is not a group-only subscription
        if ($this->application->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            if ($userIdentifier != $this->getUser()->getId() &&
                $userWithSubscriptionStatusAndType[AllSubscribedUserTableColumnModel::SUBSCRIPTION_TYPE] % 2)
            {
                if ($this->getUser()->isPlatformAdmin() ||
                    CourseManagementRights::getInstance()->is_allowed_management(
                        CourseManagementRights::TEACHER_UNSUBSCRIBE_RIGHT, $this->application->get_course_id(),
                        WeblcmsRights::TYPE_COURSE, $userIdentifier
                    ))

                {
                    $parameters = [];
                    $parameters[ToolManager::PARAM_ACTION] = Manager::ACTION_UNSUBSCRIBE;
                    $parameters[Manager::PARAM_OBJECTS] = $userIdentifier;

                    $unsubscribe_url = $urlGenerator->fromRequest($parameters);

                    $toolbar->add_item(
                        new ToolbarItem(
                            $translator->trans('DirectUnsubscribe', [], Manager::CONTEXT),
                            new FontAwesomeGlyph('minus-square', [], null, 'fas'), $unsubscribe_url,
                            ToolbarItem::DISPLAY_ICON
                        )
                    );
                }
                else
                {
                    $toolbar->add_item(
                        new ToolbarItem(
                            $translator->trans('UnsubscribeNotAvailable', [], Manager::CONTEXT),
                            new FontAwesomeGlyph('minus-square', ['text-muted'], null, 'fas'), null,
                            ToolbarItem::DISPLAY_ICON
                        )
                    );
                }

                switch ($userWithSubscriptionStatusAndType[AllSubscribedUserTableColumnModel::SUBSCRIPTION_STATUS])
                {
                    case CourseEntityRelation::STATUS_TEACHER :
                        $status_change_url = $this->application->get_status_changer_url(
                            $userIdentifier, CourseEntityRelation::STATUS_STUDENT
                        );

                        $toolbar->add_item(
                            new ToolbarItem(
                                $translator->trans('MakeStudent', [], Manager::CONTEXT),
                                new FontAwesomeGlyph('user-graduate', [], null, 'fas'), $status_change_url,
                                ToolbarItem::DISPLAY_ICON
                            )
                        );
                        break;
                    case CourseEntityRelation::STATUS_STUDENT :
                        $status_change_url = $this->application->get_status_changer_url(
                            $userIdentifier, CourseEntityRelation::STATUS_TEACHER
                        );

                        $toolbar->add_item(
                            new ToolbarItem(
                                $translator->trans('MakeTeacher', [], Manager::CONTEXT),
                                new FontAwesomeGlyph('user-tie', [], null, 'fas'), $status_change_url,
                                ToolbarItem::DISPLAY_ICON
                            )
                        );
                        break;
                }
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('UnsubscribeNotAvailable', [], Manager::CONTEXT),
                        new FontAwesomeGlyph('minus-square', ['text-muted'], null, 'fas'), null,
                        ToolbarItem::DISPLAY_ICON
                    )
                );
            }

            // if we have editing rights, display the reporting action
            $params = [];
            $params[\Chamilo\Application\Weblcms\Manager::PARAM_USERS] = $userIdentifier;
            $params[ToolManager::PARAM_ACTION] = Manager::ACTION_REPORTING;

            $reporting_url = $urlGenerator->fromRequest($params);

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Report', [], Manager::CONTEXT), new FontAwesomeGlyph('chart-pie'),
                    $reporting_url, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        $userViewAllowed = $this->getConfigurationConsulter()->getSetting(
            ['Chamilo\Application\Weblcms', 'allow_view_as_user']
        );

        // add action for view as user
        if ($userViewAllowed || $this->getUser()->isPlatformAdmin())
        {
            if ($this->application->is_allowed(WeblcmsRights::EDIT_RIGHT)) // ->get_parent()->is_teacher())
            {
                if ($userIdentifier != $this->getUser()->getId())
                {

                    $parameters = [];
                    $parameters[ToolManager::PARAM_ACTION] = Manager::ACTION_VIEW_AS;
                    $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_USERS] = $userIdentifier;
                    $view_as_url = $urlGenerator->fromRequest($parameters);

                    $toolbar->add_item(
                        new ToolbarItem(
                            $translator->trans('ViewAsUser', [], Manager::CONTEXT), new FontAwesomeGlyph('mask'),
                            $view_as_url, ToolbarItem::DISPLAY_ICON
                        )
                    );
                }
            }
        }

        $this->addAdditionalActions($toolbar, $userIdentifier);

        return $toolbar->render();
    }

}
