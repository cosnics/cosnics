<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class DirectGroupSubscribeBrowserComponent extends Manager
{

    /**
     * displays a browser for self un/enrollment in a PA group when direct subscibe is availabe when user is not
     * enrolled in agroup all groups that haven't exceeded the maximum number of members are shown with an enrollment
     * button when user is enrolled, the group to which the user belongs is displayed when self unenrollment is active
     * and no scores are given an unenroll button is displayed
     */
    public function run()
    {
        $this->settings = $this->get_settings($this->get_publication_id());
        if (! $this->settings->get_direct_subscribe_available())
        {
            throw new NotAllowedException();
        }
        
        $publication_id = $this->get_publication_id();
        $user_group = $this->get_user_group($this->get_user()->get_id());
        
        if (! $user_group)
        {
            $groups = $this->get_groups($publication_id);
            $this->user_is_member = false;
        }
        else
        {
            $this->user_is_member = true;
            $groups[] = $user_group;
        }
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->render_action_bar();
        
        // show an error message if no attempts are defined
        if (count($groups) > 0)
        {
            $html[] = $this->render_groups($groups);
        }
        
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    private function render_groups(array $groups)
    {
        // TODO date locale doesn't work
        $html = array();
        
        $image = Theme :: getInstance()->getCommonImagePath('Treemenu/Group');
        
        // loop through all the attempts and render them
        foreach ($groups as $g)
        {
            $users = $this->get_group_users($g->get_id());
            if ($this->settings->get_max_group_members() > count($users) || $this->user_is_member)
            {
                $title = $g->get_name();
                $description = $g->get_description();
                
                // mention enrolled users
                $user_string = null;
                
                if (count($users) > 0)
                {
                    foreach ($users as $user)
                    {
                        $user_string .= $user->get_firstname() . ' ' . $user->get_lastname() . ', ';
                    }
                    
                    $description .= Translation :: get('InThisGroup') . ': ' . rtrim($user_string, ' ,');
                }
                else
                {
                    $description .= '<span style="color:#f00">' . Translation :: get('NoUsersEnrolled') . '!</span>';
                }
                $actions = $this->render_toolbar($g);
                $level = $level == 1 ? 2 : 1;
                
                $html[] = Manager :: render_list_item($title, $description, '$info', $actions, $level, false, $image);
            }
        }
        
        return implode(PHP_EOL, $html);
    }

    private function render_toolbar($group)
    {
        $toolbar = new Toolbar();
        
        if (! $this->user_is_member)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Subscribe', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath('Action/Subscribe'), 
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_USER, 
                            self :: PARAM_GROUP => $group->get_id())), 
                    ToolbarItem :: DISPLAY_ICON));
        }
        elseif ($this->settings->get_unsubscribe_available() && ! $this->group_has_scores($group->get_id()))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Unsubscribe', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath('Action/Unsubscribe'), 
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE_USER, 
                            self :: PARAM_GROUP => $group->get_id())), 
                    ToolbarItem :: DISPLAY_ICON));
        }
        
        return $toolbar->as_html();
    }

    /**
     * Renders the action bar
     * 
     * @return string The html
     * @todo add toolbar items
     */
    public function render_action_bar()
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        
        $buttonToolbar = $this->buttonToolbarRenderer->getButtonToolBar();
        $commonActions = new ButtonGroup();
        $commonActions->addButton(
            new Button(
                Translation :: get('CreateGroup'), 
                Theme :: getInstance()->getCommonImagePath('Action/Browser'), 
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_USER))));
        
        $buttonToolbar->addButtonGroup($commonActions);
        
        return $this->buttonToolbarRenderer->render();
    }
}
