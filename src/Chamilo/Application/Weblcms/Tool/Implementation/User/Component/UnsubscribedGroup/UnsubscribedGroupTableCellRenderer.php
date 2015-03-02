<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\UnsubscribedGroup;

use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Component\SubSubscribedGroup\SubSubscribedPlatformGroupTableColumnModel;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * * *************************************************************************** Cell renderer for an unsubscribed
 * course group browser table.
 * 
 * @author Stijn Van Hoecke ****************************************************************************
 */
class UnsubscribedGroupTableCellRenderer extends DataClassTableCellRenderer implements 
    TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $group)
    {
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            
            case Group :: PROPERTY_NAME :
                $title = parent :: render_cell($column, $group);
                $title_short = $title;
                if (strlen($title_short) > 53)
                {
                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
                }
                return $title_short;
            
            case Group :: PROPERTY_DESCRIPTION :
                $description = strip_tags(parent :: render_cell($column, $group));
                return Utilities :: truncate_string($description);
            case Translation :: get(
                SubSubscribedPlatformGroupTableColumnModel :: USERS, 
                null, 
                \Chamilo\Core\User\Manager :: context()) :
                return $group->count_users();
            case Translation :: get(SubSubscribedPlatformGroupTableColumnModel :: SUBGROUPS) :
                return $group->count_subgroups(true, true);
        }
        
        return parent :: render_cell($column, $group);
    }

    public function get_actions($group_with_subscription_status)
    {
        // construct
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        if ($this->get_component()->get_user()->is_platform_admin() || ($this->get_component()->is_allowed(
            WeblcmsRights :: EDIT_RIGHT) && CourseManagementRights :: get_instance()->is_allowed_for_platform_group(
            CourseManagementRights :: TEACHER_DIRECT_SUBSCRIBE_RIGHT, 
            $group_with_subscription_status->get_id(), 
            $this->get_component()->get_course_id())))
        {
            
            $subscribe_group_users = $this->get_component()->get_course()->get_course_setting(
                'allow_subscribe_users_from_group', 
                $this->get_component()->get_tool_id());
            
            if ($subscribe_group_users)
            {
                // subscribe users of group
                $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION] = Manager :: ACTION_SUBSCRIBE_USERS_FROM_GROUP;
                $parameters[Manager :: PARAM_TAB] = Request :: get(Manager :: PARAM_TAB);
                $parameters[Manager :: PARAM_OBJECTS] = $group_with_subscription_status->get_id();
                
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('SubscribeUsersFromGroup'), 
                        Theme :: getInstance()->getCommonImagesPath() . 'action_copy.png', 
                        $this->get_component()->get_url($parameters), 
                        ToolbarItem :: DISPLAY_ICON));
            }
            
            // subscribe group
            $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION] = Manager :: ACTION_SUBSCRIBE_GROUPS;
            $parameters[Manager :: PARAM_TAB] = Request :: get(Manager :: PARAM_TAB);
            $parameters[Manager :: PARAM_OBJECTS] = $group_with_subscription_status->get_id();
            
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('SubscribeGroup'), 
                    Theme :: getInstance()->getCommonImagesPath() . 'action_subscribe.png', 
                    $this->get_component()->get_url($parameters), 
                    ToolbarItem :: DISPLAY_ICON));
        }
        
        // return
        return $toolbar->as_html();
    }
}
