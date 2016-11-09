<?php
namespace Chamilo\Libraries\Format\Menu\TreeMenu;

use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuArrayRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

class TreeMenu extends HtmlMenu
{

    private $name;

    private $data_provider;

    public function __construct($name, TreeMenuDataProvider $data_provider)
    {
        $this->name = $name;
        $this->data_provider = $data_provider;

        parent :: __construct($this->get_menu_items());

        $this->array_renderer = new HtmlMenuArrayRenderer();
        $this->forceCurrentUrl($this->data_provider->get_selected_tree_menu_item_url());
    }

    /**
     * Get the breadcrumbs which lead to the current category.
     *
     * @return array The breadcrumbs.
     */
    public function get_breadcrumbs()
    {
        $this->render($this->array_renderer, 'urhere');
        $breadcrumbs = $this->array_renderer->toArray();
        $trail = BreadcrumbTrail :: getInstance();
        $i = 0;
        foreach ($breadcrumbs as $crumb)
        {
            if ($i == 0)
            {
                $i ++;
                continue;
            }

            $trail->add(new Breadcrumb($crumb['url'], substr($crumb['title'], 0, strpos($crumb['title'], '(') - 1)));
        }
        return $trail;
    }

    public function get_menu_items()
    {
        $menu_items = array();
        $menu_items[] = $this->data_provider->get_tree_menu_data()->to_array();

        return $menu_items;
    }

    /**
     * Renders the menu as a tree
     *
     * @return string The HTML formatted tree
     */
    public function render_as_tree()
    {
        $renderer = new TreeMenuRenderer($this->get_tree_name());
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }

    public function get_tree_name()
    {
        return $this->name;
    }
}
