<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Manager as ToolManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
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
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CourseGroupTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const PROPERTY_NUMBER_OF_MEMBERS = 'number_of_members';

    public const TABLE_IDENTIFIER = Manager::PARAM_COURSE_GROUP;

    protected Application $application;

    protected User $user;

    public function __construct(
        User $user, Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer,
        Pager $pager
    )
    {
        $this->user = $user;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
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
                    $urlGenerator->fromRequest([ToolManager::PARAM_ACTION => Manager::ACTION_DELETE_COURSE_GROUP]),
                    $translator->trans('RemoveSelectedCourseGroups')
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
        $this->addColumn(new DataClassPropertyTableColumn(CourseGroup::class, CourseGroup::PROPERTY_NAME));
        $this->addColumn(
            new DataClassPropertyTableColumn(CourseGroup::class, CourseGroup::PROPERTY_DESCRIPTION)
        );

        $this->addColumn(
            new StaticTableColumn(
                self::PROPERTY_NUMBER_OF_MEMBERS, $this->getTranslator()->trans('NumberOfMembers', [], Manager::CONTEXT)
            )
        );

        $this->addColumn(
            new DataClassPropertyTableColumn(CourseGroup::class, CourseGroup::PROPERTY_MAX_NUMBER_OF_MEMBERS)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function legacyRender(
        Application $application, TableParameterValues $parameterValues, ArrayCollection $tableData,
        ?string $tableName = null
    ): string
    {
        $this->application = $application;

        return parent::render($parameterValues, $tableData, $tableName); // TODO: Change the autogenerated stub
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $courseGroup): string
    {
        $urlGenerator = $this->getUrlGenerator();

        switch ($column->get_name())
        {
            case CourseGroup::PROPERTY_NAME :
                if ($this->application->is_allowed(WeblcmsRights::EDIT_RIGHT) ||
                    $courseGroup->is_member($this->getUser()))
                {
                    $url = $urlGenerator->fromRequest(
                        [
                            ToolManager::PARAM_ACTION => Manager::ACTION_GROUP_DETAILS,
                            \Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP => $courseGroup->get_id()
                        ]
                    );

                    return '<a href="' . $url . '">' . $courseGroup->get_name() . '</a>';
                }
                else
                {
                    return $courseGroup->get_name();
                }
            case CourseGroup::PROPERTY_DESCRIPTION :
                return strip_tags($courseGroup->get_description());
            case self::PROPERTY_NUMBER_OF_MEMBERS :
                return (string) $courseGroup->count_members();
        }

        return parent::renderCell($column, $resultPosition, $courseGroup);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     *
     * @throws \ReflectionException
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $courseGroup): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        if ($this->application->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $parameters = [];
            $parameters[Manager::PARAM_COURSE_GROUP] = $courseGroup->get_id();
            $parameters[Manager::PARAM_COURSE_GROUP_ACTION] = Manager::ACTION_EDIT_COURSE_GROUP;
            $edit_url = $urlGenerator->fromRequest($parameters);
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                    $edit_url, ToolbarItem::DISPLAY_ICON
                )
            );

            $parameters = [];
            $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP] = $courseGroup->get_id();
            $parameters[Manager::PARAM_COURSE_GROUP_ACTION] = Manager::ACTION_DELETE_COURSE_GROUP;
            $delete_url = $urlGenerator->fromRequest($parameters);

            $confirm_messages = [];
            $confirm_messages[] = $translator->trans('DeleteConfirm', ['NAME' => $courseGroup->get_name()]);

            if ($courseGroup->hasChildren())
            {
                $confirm_messages[] = $translator->trans('DeleteConfirmChildren', [], Manager::CONTEXT);
            }

            $confirm_message = implode(' ', $confirm_messages);

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $delete_url, ToolbarItem::DISPLAY_ICON, true, null, null, $confirm_message
                )
            );
        }

        $user = $this->getUser();

        if (!$this->application->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            if ($courseGroup->is_self_registration_allowed() &&
                ($courseGroup->count_members() < $courseGroup->get_max_number_of_members() ||
                    $courseGroup->get_max_number_of_members() == 0))
            {
                if (!$courseGroup->is_member($user) && DataManager::more_subscriptions_allowed_for_user_in_group(
                        $courseGroup->getParentId(), $user->getId()
                    ))
                {
                    $parameters = [];
                    $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP] = $courseGroup->get_id();
                    $parameters[Manager::PARAM_COURSE_GROUP_ACTION] = Manager::ACTION_USER_SELF_SUBSCRIBE;
                    $subscribe_url = $urlGenerator->fromRequest($parameters);
                    $toolbar->add_item(
                        new ToolbarItem(
                            $translator->trans('Subscribe', [], Manager::CONTEXT), new FontAwesomeGlyph('plus-circle'),
                            $subscribe_url, ToolbarItem::DISPLAY_ICON
                        )
                    );
                }
            }
        }
        else
        {
            $parameters = [];
            $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP] = $courseGroup->get_id();
            $parameters[Manager::PARAM_COURSE_GROUP_ACTION] = Manager::ACTION_MANAGE_SUBSCRIPTIONS;
            $subscribe_url = $urlGenerator->fromRequest($parameters);
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Subscribe', [], Manager::CONTEXT), new FontAwesomeGlyph('plus-circle'),
                    $subscribe_url, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if (!$this->application->is_allowed(WeblcmsRights::EDIT_RIGHT) &&
            $courseGroup->is_self_unregistration_allowed() && $courseGroup->is_member($user))
        {
            $parameters = [];
            $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP] = $courseGroup->get_id();
            $parameters[Manager::PARAM_COURSE_GROUP_ACTION] = Manager::ACTION_USER_SELF_UNSUBSCRIBE;
            $unsubscribe_url = $urlGenerator->fromRequest($parameters);
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Unsubscribe', [], Manager::CONTEXT), new FontAwesomeGlyph('minus-square'),
                    $unsubscribe_url, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }
}
