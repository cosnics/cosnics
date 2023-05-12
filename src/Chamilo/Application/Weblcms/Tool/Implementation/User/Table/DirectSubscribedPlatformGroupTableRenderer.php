<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Table;

use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
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
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\User\Table
 * @author  Stijn Van Hoecke
 * @author  Sven Vanpoucke - Hogeschool Gent - Refactoring from ObjectTable to RecordTable
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DirectSubscribedPlatformGroupTableRenderer extends RecordListTableRenderer
    implements TableRowActionsSupport, TableActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_OBJECTS;

    /**
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     */
    protected Application $application;

    protected StringUtilities $stringUtilities;

    protected User $user;

    public function __construct(
        StringUtilities $stringUtilities, User $user, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager
    )
    {
        $this->user = $user;
        $this->stringUtilities = $stringUtilities;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        if ($this->application->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            // unsubscribe
            $actions->addAction(

                new TableAction(
                    $urlGenerator->fromRequest(
                        [
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_UNSUBSCRIBE_GROUPS
                        ]
                    ), $translator->trans('UnsubscribeSelectedGroups', [], Manager::CONTEXT), false
                )
            );

            // make teacher
            $actions->addAction(
                new TableAction(
                    $urlGenerator->fromRequest(
                        [
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_CHANGE_PLATFORMGROUP_STATUS_TEACHER
                        ]
                    ), $translator->trans('MakeTeacher', [], Manager::CONTEXT), false
                )
            );

            // make student
            $actions->addAction(
                new TableAction(
                    $urlGenerator->fromRequest(
                        [
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_CHANGE_PLATFORMGROUP_STATUS_STUDENT
                        ]
                    ), $translator->trans('MakeStudent', [], Manager::CONTEXT), false
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
        $this->addColumn(new DataClassPropertyTableColumn(Group::class, Group::PROPERTY_NAME));
        $this->addColumn(new DataClassPropertyTableColumn(Group::class, Group::PROPERTY_CODE));
        $this->addColumn(new DataClassPropertyTableColumn(Group::class, Group::PROPERTY_DESCRIPTION));

        $this->addColumn(
            new DataClassPropertyTableColumn(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_STATUS)
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
        TableColumn $column, TableResultPosition $resultPosition, $groupWithSubscriptionStatus
    ): string
    {
        $translator = $this->getTranslator();

        switch ($column->get_name())
        {
            case Group::PROPERTY_DESCRIPTION :
                $description = strip_tags(parent::renderCell($column, $resultPosition, $groupWithSubscriptionStatus));

                return $this->getStringUtilities()->truncate($description);
            case CourseEntityRelation::PROPERTY_STATUS :
                switch ($groupWithSubscriptionStatus[CourseEntityRelation::PROPERTY_STATUS])
                {
                    case CourseEntityRelation::STATUS_TEACHER :
                        return $translator->trans('CourseAdmin');
                    case CourseEntityRelation::STATUS_STUDENT :
                        return $translator->trans('Student');
                    default :
                        return $translator->trans('Unknown');
                }
        }

        return parent::renderCell($column, $resultPosition, $groupWithSubscriptionStatus);
    }

    public function renderTableRowActions(TableResultPosition $resultPosition, $groupWithSubscriptionStatus): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $groupIdentifier = $groupWithSubscriptionStatus[DataClass::PROPERTY_ID];

        if ($this->application->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            if ($this->getUser()->is_platform_admin() || ($this->application->is_allowed(
                        WeblcmsRights::EDIT_RIGHT
                    ) && CourseManagementRights::getInstance()->is_allowed_for_platform_group(
                        CourseManagementRights::TEACHER_UNSUBSCRIBE_RIGHT, $groupIdentifier,
                        $this->application->get_course_id()
                    )))
            {
                // unsubscribe group
                $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] =
                    Manager::ACTION_UNSUBSCRIBE_GROUPS;
                $parameters[Manager::PARAM_OBJECTS] = $groupIdentifier;

                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('UnsubscribeGroup', [], Manager::CONTEXT),
                        new FontAwesomeGlyph('minus-square'), $urlGenerator->fromRequest($parameters),
                        ToolbarItem::DISPLAY_ICON
                    )
                );
            }

            // change status
            switch ($groupWithSubscriptionStatus[CourseEntityRelation::PROPERTY_STATUS])
            {
                case CourseEntityRelation::STATUS_TEACHER :
                    $status_change_url = $this->application->get_platformgroup_status_changer_url(
                        $groupIdentifier, CourseEntityRelation::STATUS_STUDENT
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
                    $status_change_url = $this->application->get_platformgroup_status_changer_url(
                        $groupIdentifier, CourseEntityRelation::STATUS_TEACHER
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

        return $toolbar->render();
    }
}
