<?php
namespace Chamilo\Core\Repository\External\Action\Component;

use Chamilo\Core\Repository\External\Action\Manager;
use Chamilo\Core\Repository\External\Action\Menu;
use Chamilo\Core\Repository\External\Renderer\Renderer;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class BrowserComponent extends Manager implements DelegateComponent
{

    private $action_bar;

    public function render_menu()
    {
        $extra = $this->get_menu_items();
        if ($this->action_bar->get_query() && count($extra) > 0)
        {
            $search_url = '#';
            $search = array();

            $search['title'] = Translation :: get('SearchResults');

            $search['url'] = $search_url;
            $search['class'] = 'search_results';
            $extra[] = $search;
        }
        else
        {
            $search_url = null;
        }

        $menu = new Menu(
            Request :: get(\Chamilo\Core\Repository\External\Manager :: PARAM_EXTERNAL_REPOSITORY_ID),
            $this->get_parent(),
            $extra);

        if ($search_url)
        {
            $menu->forceCurrentUrl($search_url);
        }

        $html = array();
        if ($menu->count_menu_items() > 0)
        {
            $html[] = '<div style=" width: 20%; overflow: auto; float: left;">';
            $html[] = $menu->render_as_tree();
            $html[] = '</div>';
        }
        return implode(PHP_EOL, $html);
    }

    public function run()
    {
        $this->action_bar = $this->get_action_bar();
        $query = $this->action_bar->get_query();

        $html = array();

        if (isset($query) && $query != '')
        {
            $this->set_parameter(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY, $query);
        }

        $html[] = $this->render_header();
        $html[] = $this->action_bar->as_html();
        $html[] = '<div id="action_bar_browser">';

        if ($this->get_menu() == null)
        {
            $menu = $this->render_menu();
        }
        else
        {
            $menu = array();
            $menu[] = '<div style=" width: 20%; overflow: auto; float: left;">';
            $menu[] = $this->get_menu()->render_as_tree();
            $menu[] = '</div>';
            $menu = implode(PHP_EOL, $menu);
        }
        if ($menu)
        {
            $html[] = $menu;
            $html[] = '<div style=" width: 80%; overflow: auto; float: left;">';
        }
        $html[] = Renderer :: factory($this->get_parent()->get_renderer(), $this)->as_html();

        if ($menu)
        {
            $html[] = '</div>';
        }

        $html[] = '</div>';
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_condition()
    {
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            return $this->translate_search_query($query);
        }
        return null;
    }

    public function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Browser'),
                $this->get_url(),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $renderers = $this->get_parent()->get_available_renderers();

        if (count($renderers) > 1)
        {
            foreach ($renderers as $renderer)
            {
                $action_bar->add_tool_action(
                    new ToolbarItem(
                        Translation :: get(
                            (string) StringUtilities :: getInstance()->createString($renderer)->upperCamelize() . 'View',
                            null,
                            Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagePath(
                            'View/' . (string) StringUtilities :: getInstance()->createString($renderer)->upperCamelize()),
                        $this->get_url(array(\Chamilo\Core\Repository\External\Manager :: PARAM_RENDERER => $renderer)),
                        ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }

        return $action_bar;
    }
}
