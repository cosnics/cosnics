<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: wiki_actionbar.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.complex_display.wiki.component
 */
/**
 * Class that renders an action bar divided in 3 parts, a left menu for actions, a middle menu for actions and a right
 * menu for a search bar.
 */
class WikiActionbar extends ActionBarRenderer
{
    const ACTION_BAR_NAVIGATION = 'navigation';
    const TYPE_WIKI = 'wiki';

    private $links = array();

    public function add_navigation_link($link)
    {
        $this->links[self :: ACTION_BAR_NAVIGATION][] = $link;
    }

    public function set_navigation_links($links)
    {
        $this->links[self :: ACTION_BAR_NAVIGATION] = $links;
    }

    public function get_navigation_links()
    {
        return $this->links[self :: ACTION_BAR_NAVIGATION];
    }

    public function as_html()
    {
        return $this->render_wiki();
    }

    public function render_wiki()
    {
        $html = array();
        
        $html[] = '<div id="' . $this->get_name() . '_action_bar_left" class="action_bar_wiki">';
        
        $common_actions = $this->get_common_actions();
        $tool_actions = $this->get_tool_actions();
        $wiki_links = $this->get_navigation_links();
        
        $action_bar_has_common_actions = (count($common_actions) > 0);
        $action_bar_has_links = (count($wiki_links) > 0);
        $action_bar_has_tool_actions = (count($tool_actions) > 0);
        $action_bar_has_common_and_tool_actions = (count($common_actions) > 0) && (count($tool_actions) > 0);
        
        if ($action_bar_has_links)
        {
            
            $html[] = Translation :: get('Navigation');
            $html[] = '<div class="clear"></div>';
            
            $toolbar = new Toolbar();
            $html[] = '<div style="border:1px solid #4271B5;padding:3px;background-color: #faf7f7;">';
            $toolbar->set_items($wiki_links);
            $toolbar->set_type(Toolbar :: TYPE_VERTICAL);
            $html[] = $toolbar->as_html();
            $html[] = '</div><br />';
        }
        
        if ($this->get_query() && ($action_bar_has_common_actions || $action_bar_has_tool_actions))
        {
            $html[] = '<div class="divider"></div>';
        }
        
        if ($action_bar_has_common_actions)
        {
            
            $html[] = Translation :: get('PageActions');
            $html[] = '<div class="clear"></div>';
            
            $toolbar = new Toolbar();
            $html[] = '<div style="border:1px solid #4271B5;padding:3px;background-color: #faf7f7;">';
            $toolbar->set_items($common_actions);
            $toolbar->set_type(Toolbar :: TYPE_VERTICAL);
            $html[] = $toolbar->as_html();
            $html[] = '</div><br />';
        }
        
        if ($action_bar_has_common_and_tool_actions)
        {
            $html[] = '<div class="divider"></div>';
        }
        
        if ($action_bar_has_tool_actions)
        {
            $html[] = Translation :: get('Information');
            $html[] = '<div class="clear"></div>';
            
            $toolbar = new Toolbar();
            $html[] = '<div style="border:1px solid #4271B5;padding:3px;background-color: #faf7f7;">';
            $toolbar->set_items($tool_actions);
            $toolbar->set_type(Toolbar :: TYPE_VERTICAL);
            $html[] = $toolbar->as_html();
            $html[] = '</div><br />';
        }
        
        $html[] = '</div>';
        
        $html[] = ResourceManager :: getInstance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'ActionBarVertical.js');
        
        return implode(PHP_EOL, $html);
    }
}
