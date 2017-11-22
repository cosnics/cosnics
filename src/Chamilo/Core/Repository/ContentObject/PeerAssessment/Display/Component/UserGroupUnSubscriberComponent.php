<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class UserGroupUnSubscriberComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! is_null(Request::get(self::PARAM_GROUP)))
        {
            $success = $this->remove_user_from_group($this->get_user_id(), Request::get(self::PARAM_GROUP));
            $message = $success ? Translation::get('GroupUnsubscriptionSucceeded') : Translation::get(
                'NoGroupUnsubscription');
            $this->redirect(
                $message, 
                ! $success, 
                array(self::PARAM_ACTION => self::ACTION_VIEW_USER_ATTEMPT_STATUS));
        }
        
        $html = array();
        
        $html[] = $this->render_header();
        
        $groups = $this->get_groups($this->get_publication_id());
        if (count($groups) === 0)
        {
            // show an error message if no groups are defined
            $html[] = Display::error_message(Translation::get('NoGroupsDefined'));
        }
        else
        {
            $html[] = $this->render_groups($groups);
        }
        
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    private function render_groups(array $groups)
    {
        // @TODO date locale doesn't work
        $html = array();
        
        $image = Theme::getInstance()->getCommonImagePath('Treemenu/Group');
        
        // loop through all the attempts and render them
        foreach ($groups as $g)
        {
            $url = $this->get_url(
                array(self::PARAM_ACTION => self::ACTION_SUBSCRIBE_USER, self::PARAM_GROUP => $g->get_id()));
            $title = '<a href="' . $url . '">' . $g->get_name() . '</a>';
            $description = $g->get_description();
            
            $users = $this->get_group_users($g->get_id());
            
            // mention enrolled users
            $user_string = null;
            
            if (count($users) > 0)
            {
                foreach ($users as $user)
                {
                    $user_string .= $user->get_firstname() . ' ' . $user->get_lastname() . ', ';
                }
                
                $description .= Translation::get('InThisGroup') . ': ' . rtrim($user_string, ' ,');
            }
            else
            {
                $description .= '<span style="color:#f00">' . Translation::get('NoUsersEnrolled') . '!</span>';
            }
            $actions = $this->render_toolbar($g);
            $level = $level == 1 ? 2 : 1;
            
            $html[] = $this->render_list_item($title, $description, '$info', $actions, $level, false, $image);
        }
        
        return implode(PHP_EOL, $html);
    }

    private function render_toolbar($group)
    {
        $toolbar = new Toolbar();
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Subscribe'), 
                $this->get_url(
                    array(self::PARAM_ACTION => self::ACTION_SUBSCRIBE_USER, self::PARAM_GROUP => $group->get_id())), 
                ToolbarItem::DISPLAY_ICON));
        
        return $toolbar->as_html();
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_VIEW_USER_ATTEMPT_STATUS)), 
                Translation::get('Overview')));
    }
}