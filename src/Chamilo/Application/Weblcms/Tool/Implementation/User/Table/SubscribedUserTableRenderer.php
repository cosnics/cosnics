<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Table;

use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
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
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableParameterValues;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\User\Table
 * @author  Stijn Van Hoecke
 * @author  Sven Vanpoucke - Hogeschool Gent - Refactoring from ObjectTable to RecordTable
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SubscribedUserTableRenderer extends RecordListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const DEFAULT_ORDER_COLUMN_INDEX = 1;

    public const PROPERTY_SUBSCRIPTION_STATUS = 'subscription_status';
    public const PROPERTY_SUBSCRIPTION_TYPE = 'subscription_type';

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
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->user = $user;
        $this->configurationConsulter = $configurationConsulter;
        $this->chamiloRequest = $chamiloRequest;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
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

        if ($this->application->is_course_admin($this->getUser()))
        {
            // if we are not editing groups
            if (!$this->getChamiloRequest()->query->get(\Chamilo\Application\Weblcms\Manager::PARAM_GROUP))
            {
                $actions->addAction(
                    new TableAction(
                        $urlGenerator->fromRequest(
                            [
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_UNSUBSCRIBE
                            ]
                        ), $translator->trans('UnsubscribeSelected', [], Manager::CONTEXT), false
                    )
                );

                // make teacher
                $actions->addAction(
                    new TableAction(
                        $urlGenerator->fromRequest(
                            [
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_CHANGE_USER_STATUS_TEACHER
                            ]
                        ), $translator->trans('MakeTeacher', [], Manager::CONTEXT), false
                    )
                );

                // make student
                $actions->addAction(
                    new TableAction(
                        $urlGenerator->fromRequest(
                            [
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_CHANGE_USER_STATUS_STUDENT
                            ]
                        ), $translator->trans('MakeStudent', [], Manager::CONTEXT), false
                    )
                );
            }
        }

        return $actions;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    protected function initializeColumns()
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
        TableColumn $column, TableResultPosition $resultPosition, $userWithSubscriptionStatus
    ): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        switch ($column->get_name())
        {
            case CourseEntityRelation::PROPERTY_STATUS :
                switch ($userWithSubscriptionStatus[CourseEntityRelation::PROPERTY_STATUS])
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
                $email = $userWithSubscriptionStatus[User::PROPERTY_EMAIL];

                $activeOnlineEmailEditor = $this->getConfigurationConsulter()->getSetting(
                    ['Chamilo\Core\Admin', 'active_online_email_editor']
                );

                if ($activeOnlineEmailEditor)
                {
                    $parameters = [];
                    $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = Manager::ACTION_EMAIL;
                    $parameters[Manager::PARAM_OBJECTS] = $userWithSubscriptionStatus[DataClass::PROPERTY_ID];

                    $email_url = $urlGenerator->fromRequest($parameters);
                }
                else
                {
                    $email_url = 'mailto:' . $email;
                }

                return '<a href="' . $email_url . '">' . $email . '</a>';
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

        $user_id = $userWithSubscriptionStatus[DataClass::PROPERTY_ID];

        // always show details
        $parameters = [];
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = Manager::ACTION_USER_DETAILS;
        $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_USERS] = $user_id;
        $details_url = $urlGenerator->fromRequest($parameters);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Details', [], Manager::CONTEXT), new FontAwesomeGlyph('info-circle'), $details_url,
                ToolbarItem::DISPLAY_ICON
            )
        );

        // display the actions to change the individual status and unsubscribe
        // if:
        // (1) the user is platform or course admin
        // AND
        // (2) the row is not the current user
        // AND
        // (3) we are not editing groups
        if ($this->application->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $group_id = $this->getChamiloRequest()->query->get(\Chamilo\Application\Weblcms\Manager::PARAM_GROUP);
            if ($user_id != $this->getUser()->getId() && !isset($group_id))
            {
                if ($this->getUser()->is_platform_admin() ||
                    CourseManagementRights::getInstance()->is_allowed_management(
                        CourseManagementRights::TEACHER_UNSUBSCRIBE_RIGHT, $this->application->get_course_id(),
                        WeblcmsRights::TYPE_COURSE, $user_id
                    ))

                {
                    $parameters = [];
                    $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = Manager::ACTION_UNSUBSCRIBE;
                    $parameters[Manager::PARAM_OBJECTS] = $user_id;
                    $unsubscribe_url = $urlGenerator->fromRequest($parameters);

                    $toolbar->add_item(
                        new ToolbarItem(
                            $translator->trans('Unsubscribe', [], Manager::CONTEXT),
                            new FontAwesomeGlyph('minus-square'), $unsubscribe_url, ToolbarItem::DISPLAY_ICON
                        )
                    );
                }
                else
                {
                    $toolbar->add_item(
                        new ToolbarItem(
                            $translator->trans('UnsubscribeNotAvailable', [], Manager::CONTEXT), new FontAwesomeGlyph(
                            'minus-square', ['text-muted']
                        ), null, ToolbarItem::DISPLAY_ICON
                        )
                    );
                }

                switch ($userWithSubscriptionStatus[CourseEntityRelation::PROPERTY_STATUS])
                {
                    case CourseEntityRelation::STATUS_TEACHER :
                        $status_change_url = $this->application->get_status_changer_url(
                            $user_id, CourseEntityRelation::STATUS_STUDENT
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
                            $user_id, CourseEntityRelation::STATUS_TEACHER
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
                        $translator->trans('UnsubscribeNotAvailable', [], Manager::CONTEXT), new FontAwesomeGlyph(
                        'minus-square', ['text-muted']
                    ), null, ToolbarItem::DISPLAY_ICON
                    )
                );
            }

            // if we have editing rights, display the reporting action
            $params = [];
            $params[\Chamilo\Application\Weblcms\Manager::PARAM_USERS] = $user_id;
            $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = Manager::ACTION_REPORTING;
            $reporting_url = $urlGenerator->fromRequest($params);

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Report', [], Manager::CONTEXT), new FontAwesomeGlyph('chart-pie'),
                    $reporting_url, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        // add action for view as user
        $userViewAllowed = $this->getConfigurationConsulter()->getSetting(
            ['Chamilo\Application\Weblcms', 'allow_view_as_user']
        );

        if ($userViewAllowed || $this->getUser()->is_platform_admin())
        {
            if ($this->application->is_allowed(WeblcmsRights::EDIT_RIGHT)) // get_parent()->is_teacher())
            {
                if ($user_id != $this->getUser()->getId())
                {
                    $parameters = [];
                    $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = Manager::ACTION_VIEW_AS;
                    $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_USERS] = $user_id;
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

        return $toolbar->render();
    }

}
