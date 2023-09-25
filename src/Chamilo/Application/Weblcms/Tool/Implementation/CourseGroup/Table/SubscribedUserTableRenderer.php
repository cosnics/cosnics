<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupUserRelation;
use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
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
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SubscribedUserTableRenderer extends RecordListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const TABLE_IDENTIFIER = \Chamilo\Application\Weblcms\Manager::PARAM_USERS;

    /**
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     */
    protected Application $application;

    protected ConfigurationConsulter $configurationConsulter;

    protected DatetimeUtilities $datetimeUtilities;

    protected User $user;

    protected UserService $userService;

    public function __construct(
        UserService $userService, DatetimeUtilities $datetimeUtilities, ConfigurationConsulter $configurationConsulter,
        User $user, Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer,
        Pager $pager, DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->user = $user;
        $this->configurationConsulter = $configurationConsulter;
        $this->datetimeUtilities = $datetimeUtilities;
        $this->userService = $userService;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        if ($this->application->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $actions->addAction(
                new TableAction(
                    $urlGenerator->fromRequest(
                        [\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_GROUP_DETAILS]
                    ), $translator->trans('UnsubscribeUsers', [], Manager::CONTEXT)
                )
            );
        }

        return $actions;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    protected function initializeColumns(): void
    {
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
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                CourseGroupUserRelation::class, CourseGroupUserRelation::PROPERTY_SUBSCRIPTION_TIME
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     */
    public function legacyRender(
        Application $application, TableParameterValues $parameterValues, ArrayCollection $tableData,
        ?string $tableName = null
    ): string
    {
        $this->application = $application;

        return parent::render($parameterValues, $tableData, $tableName); // TODO: Change the autogenerated stub
    }

    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $user): string
    {
        $translator = $this->getTranslator();

        switch ($column->get_name())
        {
            case User::PROPERTY_EMAIL :
                return '<a href="mailto:' . $user[User::PROPERTY_EMAIL] . '">' . $user[User::PROPERTY_EMAIL] . '</a>';
            case CourseGroupUserRelation::PROPERTY_SUBSCRIPTION_TIME :
                $subscriptionTime = $user[CourseGroupUserRelation::PROPERTY_SUBSCRIPTION_TIME];

                if ($subscriptionTime)
                {
                    return $this->getDatetimeUtilities()->formatLocaleDate(
                        $translator->trans('SubscriptionTimeFormat', [], Manager::CONTEXT), (int) $subscriptionTime
                    );
                }

                return '';
        }

        return parent::renderCell($column, $resultPosition, $user);
    }

    public function renderTableRowActions(TableResultPosition $resultPosition, $userRecord): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $course_group = $this->application->get_course_group();
        $user = $this->getUserService()->findUserByIdentifier($userRecord[DataClass::PROPERTY_ID]);

        if ($this->application->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $parameters = [];

            $parameters[Manager::PARAM_COURSE_GROUP_ACTION] = Manager::ACTION_UNSUBSCRIBE;
            $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_USERS] = $user->getId();
            $parameters[Manager::PARAM_COURSE_GROUP] = $course_group->getId();

            $unsubscribe_url = $urlGenerator->fromRequest($parameters);
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Unsubscribe', [], Manager::CONTEXT), new FontAwesomeGlyph(
                    'minus-square', ['text-muted']
                ), $unsubscribe_url, ToolbarItem::DISPLAY_ICON, true
                )
            );
        }

        if (!$this->application->is_allowed(WeblcmsRights::EDIT_RIGHT) &&
            $course_group->is_self_unregistration_allowed() && $course_group->is_member($user) &&
            $this->getUser()->getId() == $user->getId())
        {
            $parameters = [];
            $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP] = $course_group->getId();
            $parameters[Manager::PARAM_COURSE_GROUP_ACTION] = Manager::ACTION_USER_SELF_UNSUBSCRIBE;
            $unsubscribe_url = $urlGenerator->fromRequest($parameters);
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Unsubscribe', [], Manager::CONTEXT), new FontAwesomeGlyph(
                    'minus-square', ['text-muted']
                ), $unsubscribe_url, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }
}
