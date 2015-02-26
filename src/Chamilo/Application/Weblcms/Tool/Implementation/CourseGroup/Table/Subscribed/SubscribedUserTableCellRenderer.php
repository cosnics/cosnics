<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\Subscribed;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: course_group_subscribed_user_browser_table_cell_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.tool.course_group.component.user_table
 */
class SubscribedUserTableCellRenderer extends DataClassTableCellRenderer implements 
    TableCellRendererActionsColumnSupport
{
    
    // Inherited
    public function render_cell($column, $user)
    {
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case User :: PROPERTY_EMAIL :
                return '<a href="mailto:' . $user->get_email() . '">' . $user->get_email() . '</a>';
        }
        return parent :: render_cell($column, $user);
    }

    public function get_actions($user)
    {
        $toolbar = new Toolbar();
        $browser = $this->get_component();
        if ($browser->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $parameters = array();
            $parameters[Manager :: PARAM_COURSE_GROUP_ACTION] = Manager :: ACTION_UNSUBSCRIBE;
            $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_USERS] = $user->get_id();
            $parameters[Manager :: PARAM_COURSE_GROUP] = $browser->get_course_group()->get_id();
            $unsubscribe_url = $browser->get_url($parameters);
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Unsubscribe'), 
                    Theme :: getInstance()->getCommonImagePath() . 'action_unsubscribe.png', 
                    $unsubscribe_url, 
                    ToolbarItem :: DISPLAY_ICON, 
                    true));
        }
        
        $course_group = $browser->get_course_group();
        
        if (! $browser->is_allowed(WeblcmsRights :: EDIT_RIGHT) && $course_group->is_self_unregistration_allowed() &&
             $course_group->is_member($user) && $browser->get_user()->get_id() == $user->get_id())
        {
            $parameters = array();
            $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_COURSE_GROUP] = $course_group->get_id();
            $parameters[Manager :: PARAM_COURSE_GROUP_ACTION] = Manager :: ACTION_USER_SELF_UNSUBSCRIBE;
            $unsubscribe_url = $browser->get_url($parameters);
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Unsubscribe'), 
                    Theme :: getInstance()->getCommonImagePath() . 'action_unsubscribe.png', 
                    $unsubscribe_url, 
                    ToolbarItem :: DISPLAY_ICON));
        }
        
        return $toolbar->as_html();
    }
}
