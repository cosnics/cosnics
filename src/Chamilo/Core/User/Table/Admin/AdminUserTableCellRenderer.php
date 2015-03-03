<?php
namespace Chamilo\Core\User\Table\Admin;

use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Template\LoginTemplate;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Cell renderer for the user object browser table
 */
class AdminUserTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{
    // Inherited
    public function render_cell($column, $user)
    {
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case User :: PROPERTY_STATUS :
                if ($user->get_status() == '1')
                {
                    return Translation :: get('CourseAdmin');
                }
                else
                {
                    return Translation :: get('Student');
                }
            case User :: PROPERTY_PLATFORMADMIN :
                return Utilities :: display_true_false_icon($user->get_platformadmin());
            case User :: PROPERTY_ACTIVE :
                return Utilities :: display_true_false_icon($user->get_active());
        }
        
        return parent :: render_cell($column, $user);
    }

    /**
     * Gets the action links to display
     * 
     * @param $user The user for which the action links should be returned
     * @return string A HTML representation of the action links
     */
    public function get_actions($user)
    {
        $toolbar = new Toolbar();
        
        if ($this->get_component()->get_user()->is_platform_admin())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath('action_edit'), 
                    $this->get_component()->get_user_editing_url($user), 
                    ToolbarItem :: DISPLAY_ICON));
            
            $params = array();
            $params[Manager :: PARAM_USER_USER_ID] = $user->get_id();
            $toolbar->add_item(
                new ToolBarItem(
                    Translation :: get('Detail'), 
                    Theme :: getInstance()->getCommonImagePath('action_details'), 
                    $this->get_component()->get_user_detail_url($user->get_id()), 
                    ToolbarItem :: DISPLAY_ICON));
            
            $toolbar->add_item(
                new ToolBarItem(
                    Translation :: get('Report'), 
                    Theme :: getInstance()->getCommonImagePath('action_reporting'), 
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_REPORTING, 
                            \Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Manager :: PARAM_ACTION => \Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Manager :: ACTION_VIEW, 
                            \Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Manager :: PARAM_TEMPLATE_ID => LoginTemplate :: TEMPLATE_ID, 
                            Manager :: PARAM_USER_USER_ID => $user->get_id())), 
                    ToolbarItem :: DISPLAY_ICON));
            
            $toolbar->add_item(
                new ToolBarItem(
                    Translation :: get('ViewQuota'), 
                    Theme :: getInstance()->getCommonImagePath('action_browser'), 
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_VIEW_QUOTA, 
                            Manager :: PARAM_USER_USER_ID => $user->get_id())), 
                    ToolbarItem :: DISPLAY_ICON));
            
            if (PlatformSetting :: get('active_online_email_editor'))
            {
                $toolbar->add_item(
                    new ToolBarItem(
                        Translation :: get('SendEmail'), 
                        Theme :: getInstance()->getCommonImagePath('action_email'), 
                        $this->get_component()->get_email_user_url($user), 
                        ToolbarItem :: DISPLAY_ICON));
            }
        }
        
        $toolbar->add_item(
            new ToolBarItem(
                Translation :: get('Metadata', null, Utilities :: COMMON_LIBRARIES), 
                Theme :: getInstance()->getCommonImagePath('action_metadata'), 
                $this->get_component()->get_edit_metadata_url($user), 
                ToolbarItem :: DISPLAY_ICON));
        
        if ($user->get_id() != \Chamilo\Libraries\Platform\Session\Session :: get_user_id())
        {
            if ($this->get_component()->get_user()->is_platform_admin())
            {
                $toolbar->add_item(
                    new ToolBarItem(
                        Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES), 
                        Theme :: getInstance()->getCommonImagePath('action_delete'), 
                        $this->get_component()->get_user_delete_url($user), 
                        ToolbarItem :: DISPLAY_ICON, 
                        true));
            }
            else
            {
                $toolbar->add_item(
                    new ToolBarItem(
                        Translation :: get('DeleteNA', null, Utilities :: COMMON_LIBRARIES), 
                        Theme :: getInstance()->getCommonImagePath('action_delete_na'), 
                        null, 
                        ToolbarItem :: DISPLAY_ICON));
            }
            
            if ($this->get_component()->get_user()->is_platform_admin())
            {
                $toolbar->add_item(
                    new ToolBarItem(
                        Translation :: get('LoginAsUser'), 
                        Theme :: getInstance()->getCommonImagePath('action_login'), 
                        $this->get_component()->get_change_user_url($user), 
                        ToolbarItem :: DISPLAY_ICON));
            }
        }
        else
        {
            $toolbar->add_item(
                new ToolBarItem(
                    Translation :: get('DeleteNA', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath('action_delete_na'), 
                    null, 
                    ToolbarItem :: DISPLAY_ICON));
        }
        
        return $toolbar->as_html();
    }
}
