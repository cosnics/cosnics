<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\CourseGroup;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.lib.weblcms.tool.course_group.component.user_table
 */
class CourseGroupTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     *
     * @param TableColumn $column
     * @param CourseGroup $course_group
     *
     * @return string
     */
    public function render_cell($column, $course_group)
    {
        // Add special features here
        switch ($column->get_name())
        {
            case CourseGroup::PROPERTY_NAME :
                if ($this->get_component()->is_allowed(WeblcmsRights::EDIT_RIGHT) ||
                    $course_group->is_member($this->get_component()->get_user()))
                {
                    $url = $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_GROUP_DETAILS,
                            \Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP => $course_group->get_id()
                        )
                    );

                    return '<a href="' . $url . '">' . $course_group->get_name() . '</a>';
                }
                else
                {
                    return $course_group->get_name();
                }
            case CourseGroup::PROPERTY_DESCRIPTION :
                return strip_tags($course_group->get_description());
            case CourseGroupTableColumnModel::COLUMN_NUMBER_OF_MEMBERS :
                return $course_group->count_members();
        }

        return parent::render_cell($column, $course_group);
    }

    public function get_actions($course_group)
    {
        $toolbar = new Toolbar();
        $parameters = array();
        $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP] = $course_group->get_id();

        if ($this->get_component()->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $parameters = array();
            $parameters[Manager::PARAM_COURSE_GROUP] = $course_group->get_id();
            $parameters[Manager::PARAM_COURSE_GROUP_ACTION] = Manager::ACTION_EDIT_COURSE_GROUP;
            $edit_url = $this->get_component()->get_url($parameters);
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('pencil'),
                    $edit_url, ToolbarItem::DISPLAY_ICON
                )
            );

            $parameters = array();
            $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP] = $course_group->get_id();
            $parameters[Manager::PARAM_COURSE_GROUP_ACTION] = Manager::ACTION_DELETE_COURSE_GROUP;
            $delete_url = $this->get_component()->get_url($parameters);

            $confirm_messages = array();
            $confirm_messages[] = Translation::get('DeleteConfirm', array('NAME' => $course_group->geT_name())); // TODO
            // ::
            // Better
            if ($course_group->has_children())
            {
                $confirm_messages[] = Translation::get('DeleteConfirmChildren');
            }
            $confirm_message = implode(" ", $confirm_messages);

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('times'),
                    $delete_url, ToolbarItem::DISPLAY_ICON, true, null, null, $confirm_message
                )
            );
        }

        $user = $this->get_component()->get_user();

        if (!$this->get_component()->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            if ($course_group->is_self_registration_allowed() &&
                ($course_group->count_members() < $course_group->get_max_number_of_members() ||
                    $course_group->get_max_number_of_members() == 0))
            {
                if (!$course_group->is_member($user) && DataManager::more_subscriptions_allowed_for_user_in_group(
                        $course_group->get_parent_id(), $user->get_id()
                    ))
                {
                    $parameters = array();
                    $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP] = $course_group->get_id();
                    $parameters[Manager::PARAM_COURSE_GROUP_ACTION] = Manager::ACTION_USER_SELF_SUBSCRIBE;
                    $subscribe_url = $this->get_component()->get_url($parameters);
                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation::get('Subscribe'), new FontAwesomeGlyph('plus-circle'), $subscribe_url,
                            ToolbarItem::DISPLAY_ICON
                        )
                    );
                }
            }
        }
        else
        {
            $parameters = array();
            $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP] = $course_group->get_id();
            $parameters[Manager::PARAM_COURSE_GROUP_ACTION] = Manager::ACTION_MANAGE_SUBSCRIPTIONS;
            $subscribe_url = $this->get_component()->get_url($parameters);
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Subscribe'), new FontAwesomeGlyph('plus-circle'), $subscribe_url,
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if (!$this->get_component()->is_allowed(WeblcmsRights::EDIT_RIGHT) &&
            $course_group->is_self_unregistration_allowed() && $course_group->is_member($user))
        {
            $parameters = array();
            $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP] = $course_group->get_id();
            $parameters[Manager::PARAM_COURSE_GROUP_ACTION] = Manager::ACTION_USER_SELF_UNSUBSCRIBE;
            $unsubscribe_url = $this->get_component()->get_url($parameters);
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Unsubscribe'), new FontAwesomeGlyph('minus-square'), $unsubscribe_url,
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->as_html();
    }
}
