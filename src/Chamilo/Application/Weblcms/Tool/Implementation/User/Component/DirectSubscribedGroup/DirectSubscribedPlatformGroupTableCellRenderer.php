<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\DirectSubscribedGroup;

use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Cell renderer for a direct subscribed course group browser table.
 *
 * @author Stijn Van Hoecke
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring from ObjectTable to RecordTable
 */
class DirectSubscribedPlatformGroupTableCellRenderer extends RecordTableCellRenderer implements
    TableCellRendererActionsColumnSupport
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Renders a single cell
     *
     * @param RecordTableColumn $column
     * @param string[] $group_with_subscription_status
     *
     * @return String
     */
    public function render_cell($column, $group_with_subscription_status)
    {
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case Group::PROPERTY_DESCRIPTION :
                return \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(
                    \Chamilo\Core\Group\Storage\DataClass\Group::class_name(),
                    $group_with_subscription_status[\Chamilo\Core\Group\Storage\DataClass\Group::PROPERTY_ID]
                )->get_fully_qualified_name();
            case CourseEntityRelation::PROPERTY_STATUS :
                switch ($group_with_subscription_status[CourseEntityRelation::PROPERTY_STATUS])
                {
                    case CourseEntityRelation::STATUS_TEACHER :
                        return Translation::get('CourseAdmin');
                    case CourseEntityRelation::STATUS_STUDENT :
                        return Translation::get('Student');
                    default :
                        return Translation::get('Unknown');
                }
        }

        return parent::render_cell($column, $group_with_subscription_status);
    }

    /**
     * Gets the action links to display
     *
     * @param string[] $group_with_subscription_status
     *
     * @return string
     */
    public function get_actions($group_with_subscription_status)
    {
        $group_id = $group_with_subscription_status[Group::PROPERTY_ID];

        $toolbar = new Toolbar();

        if ($this->get_component()->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            if ($this->get_component()->get_user()->is_platform_admin() || ($this->get_component()->is_allowed(
                        WeblcmsRights::EDIT_RIGHT
                    ) && CourseManagementRights::getInstance()->is_allowed_for_platform_group(
                        CourseManagementRights::TEACHER_UNSUBSCRIBE_RIGHT,
                        $group_id,
                        $this->get_component()->get_course_id()
                    )))
            {
                // unsubscribe group
                $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] =
                    Manager::ACTION_UNSUBSCRIBE_GROUPS;
                $parameters[Manager::PARAM_TAB] = Request::get(Manager::PARAM_TAB);
                $parameters[Manager::PARAM_OBJECTS] = $group_id;

                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('UnsubscribeGroup'),
                        Theme::getInstance()->getCommonImagePath('Action/Unsubscribe'),
                        $this->get_component()->get_url($parameters),
                        ToolbarItem::DISPLAY_ICON
                    )
                );
            }

            $weblcms_manager_namespace = \Chamilo\Application\Weblcms\Manager::context();

            // change status
            switch ($group_with_subscription_status[CourseEntityRelation::PROPERTY_STATUS])
            {
                case CourseEntityRelation::STATUS_TEACHER :
                    $status_change_url = $this->get_component()->get_platformgroup_status_changer_url(
                        $group_id,
                        CourseEntityRelation::STATUS_STUDENT
                    );

                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation::get('MakeStudent'),
                            Theme::getInstance()->getImagePath($weblcms_manager_namespace, 'Action/SubscribeStudent'),
                            $status_change_url,
                            ToolbarItem::DISPLAY_ICON
                        )
                    );

                    break;
                case CourseEntityRelation::STATUS_STUDENT :
                    $status_change_url = $this->get_component()->get_platformgroup_status_changer_url(
                        $group_id,
                        CourseEntityRelation::STATUS_TEACHER
                    );

                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation::get('MakeTeacher'),
                            Theme::getInstance()->getImagePath($weblcms_manager_namespace, 'Action/SubscribeTeacher'),
                            $status_change_url,
                            ToolbarItem::DISPLAY_ICON
                        )
                    );

                    break;
            }
        }

        return $toolbar->as_html();
    }
}
