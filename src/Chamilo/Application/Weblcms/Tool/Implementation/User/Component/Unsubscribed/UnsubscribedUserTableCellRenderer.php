<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\Unsubscribed;

use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 * * *************************************************************************** Cell renderer for an unsubscribed
 * course user browser table.
 * 
 * @author Stijn Van Hoecke ****************************************************************************
 */
class UnsubscribedUserTableCellRenderer extends DataClassTableCellRenderer implements 
    TableCellRendererActionsColumnSupport
{
    
    // **************************************************************************
    // GENERAL FUNCTIONS
    // **************************************************************************
    // Inherited
    /**
     * Renders a given cell.
     * 
     * @param type $column
     * @param type $user_with_subscription_status User from the advanced join query in weblcms database class that
     *        includes his subscription status.
     * @return type
     */
    public function render_cell($column, $user_with_subscription_status)
    {
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case User::PROPERTY_STATUS :
                switch ($user_with_subscription_status->get_status())
                {
                    case CourseEntityRelation::STATUS_TEACHER :
                        return Translation::get('CourseAdmin');
                    case CourseEntityRelation::STATUS_STUDENT :
                        return Translation::get('Student');
                    default :
                        return Translation::get('Unknown');
                }
            case User::PROPERTY_PLATFORMADMIN :
                if ($user_with_subscription_status->get_platformadmin() == '1')
                {
                    return Translation::get('PlatformAdministrator');
                }
                else
                {
                    return '';
                }
            case User::PROPERTY_EMAIL :
                $email_url = 'mailto:' . $user_with_subscription_status->get_email();
                return '<a href="' . $email_url . '">' . $user_with_subscription_status->get_email() . '</a>';
        }
        return parent::render_cell($column, $user_with_subscription_status);
    }

    /**
     * Gets the action links to display
     * 
     * @param User $user The user for which the action links should be returned
     * @return string A HTML representation of the action links
     */
    public function get_actions($user_with_subscription_status)
    {
        // construct
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);
        
        if ($this->get_component()->get_user()->is_platform_admin() || ($this->get_component()->is_allowed(
            WeblcmsRights::EDIT_RIGHT) && CourseManagementRights::getInstance()->is_allowed(
            CourseManagementRights::TEACHER_DIRECT_SUBSCRIBE_RIGHT, 
            $this->get_component()->get_course_id(), 
            CourseManagementRights::TYPE_COURSE, 
            $user_with_subscription_status->get_id())))
        
        {
            // subscribe regular student
            $parameters = array();
            $parameters[Manager::PARAM_OBJECTS] = $user_with_subscription_status->get_id();
            $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = Manager::ACTION_SUBSCRIBE;
            $parameters[Manager::PARAM_TAB] = Request::get(Manager::PARAM_TAB);
            $subscribe_url = $this->get_component()->get_url($parameters);
            
            $weblcms_manager_namespace = \Chamilo\Application\Weblcms\Manager::context();
            
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('SubscribeAsStudent'), 
                    Theme::getInstance()->getImagePath($weblcms_manager_namespace, 'Action/SubscribeStudent'), 
                    $subscribe_url, 
                    ToolbarItem::DISPLAY_ICON));
            
            // subscribe as course admin
            $parameters = array();
            $parameters[Manager::PARAM_OBJECTS] = $user_with_subscription_status->get_id();
            $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = Manager::ACTION_SUBSCRIBE_AS_ADMIN;
            $parameters[Manager::PARAM_TAB] = Request::get(Manager::PARAM_TAB);
            $subscribe_url = $this->get_component()->get_url($parameters);
            
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('SubscribeAsTeacher'), 
                    Theme::getInstance()->getImagePath($weblcms_manager_namespace, 'Action/SubscribeTeacher'), 
                    $subscribe_url, 
                    ToolbarItem::DISPLAY_ICON));
        }
        elseif ($this->get_component()->get_user()->is_platform_admin() || ($this->get_component()->is_allowed(
            WeblcmsRights::EDIT_RIGHT) && CourseManagementRights::getInstance()->is_allowed(
            CourseManagementRights::TEACHER_REQUEST_SUBSCRIBE_RIGHT, 
            $this->get_component()->get_course_id(), 
            CourseManagementRights::TYPE_COURSE, 
            $user_with_subscription_status->get_id())))
        
        {
            if (! \Chamilo\Application\Weblcms\Storage\DataManager::is_user_requested_for_course(
                $user_with_subscription_status->get_id(), 
                $this->get_component()->get_course_id()))
            {
                $parameters = array();
                $parameters[Manager::PARAM_OBJECTS] = $user_with_subscription_status->get_id();
                $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = Manager::ACTION_REQUEST_SUBSCRIBE_USER;
                $parameters[Manager::PARAM_TAB] = Request::get(Manager::PARAM_TAB);
                $subscribe_request_url = $this->get_component()->get_url($parameters);
                
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('RequestUser'), 
                        Theme::getInstance()->getImagePath(
                            'Chamilo\Application\Weblcms\Tool\Implementation\User', 
                            'Action/RequestSubscribeUser'), 
                        $subscribe_request_url, 
                        ToolbarItem::DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('UserRequestPending'), 
                        Theme::getInstance()->getCommonImagePath('Action/Period'), 
                        null, 
                        ToolbarItem::DISPLAY_ICON));
            }
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('SubscribeNA'), 
                    Theme::getInstance()->getCommonImagePath('Action/SubscribeNa'), 
                    null, 
                    ToolbarItem::DISPLAY_ICON));
        }
        
        // return
        return $toolbar->as_html();
    }
}
