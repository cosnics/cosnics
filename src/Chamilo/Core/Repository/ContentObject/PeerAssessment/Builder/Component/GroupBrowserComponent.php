<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class GroupBrowserComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->is_allowed(self::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }
        
        $publication_id = $this->get_publication_id();
        $groups = $this->get_groups($publication_id);
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->render_action_bar();
        $html[] = '<div class="context_info alert alert-warning">' . Translation::get('GroupInfoMessage') . '</div>';
        
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
        
        $image = Theme::getInstance()->getCommonImagePath('Treemenu/Group');
        
        // loop through all the attempts and render them
        foreach ($groups as $g)
        {
            $url = $this->get_url(
                array(self::PARAM_ACTION => self::ACTION_EDIT_GROUP, self::PARAM_GROUP => $g->get_id()));
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
            
            $html[] = \Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager::render_list_item(
                $title, 
                $description, 
                '$info', 
                $actions, 
                $level, 
                false, 
                $image);
        }
        
        return implode(PHP_EOL, $html);
    }

    private function render_toolbar($group)
    {
        $toolbar = new Toolbar();
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Edit'), 
                $this->get_url(
                    array(self::PARAM_ACTION => self::ACTION_EDIT_GROUP, self::PARAM_GROUP => $group->get_id())), 
                ToolbarItem::DISPLAY_ICON));
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Delete', null, Utilities::COMMON_LIBRARIES),
                new FontAwesomeGlyph('times'),
                $this->get_url(
                    array(self::PARAM_ACTION => self::ACTION_DELETE_GROUP, self::PARAM_GROUP => $group->get_id())), 
                ToolbarItem::DISPLAY_ICON));
        
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
                Translation::get('CreateGroup'), 
                Theme::getInstance()->getCommonImagePath('Action/Browser'), 
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_CREATE_GROUP))));
        
        $buttonToolbar->addButtonGroup($commonActions);
        
        return $this->buttonToolbarRenderer->render();
    }
}
