<?php
namespace Chamilo\Core\Repository\External\Action;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuArrayRenderer;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;

/**
 * This class provides a navigation menu to allow a user to browse through his reservations categories
 * 
 * @author Sven Vanpoucke
 */
class Menu extends HtmlMenu
{
    const TREE_NAME = __CLASS__;
    const ACTION_CREATE = 'create';
    const ACTION_ALL_VIDEOS = 'all_videos';
    const ACTION_MY_VIDEOS = 'my_videos';

    private $current_item;

    /**
     * The array renderer used to determine the breadcrumbs.
     */
    private $array_renderer;

    private $external_repository_manager;

    private $menu_items;

    public function __construct($current_item, $external_repository_manager, $menu_items)
    {
        $this->current_item = $current_item;
        $this->external_repository_manager = $external_repository_manager;
        $this->menu_items = $menu_items;
        // $menu = $this->get_menu();
        parent::__construct($menu_items);
        
        $this->array_renderer = new HtmlMenuArrayRenderer();
        $this->forceCurrentUrl($this->get_url());
    }

    public function get_menu_items()
    {
        return $this->menu_items;
    }

    public function count_menu_items()
    {
        return count($this->menu_items);
    }

    private function get_url()
    {
        return $this->external_repository_manager->get_url();
    }

    /**
     * Get the breadcrumbs which lead to the current category.
     * 
     * @return array The breadcrumbs.
     */
    public function get_breadcrumbs()
    {
        $trail = BreadcrumbTrail::getInstance();
        $this->render($this->array_renderer, 'urhere');
        $breadcrumbs = $this->array_renderer->toArray();
        foreach ($breadcrumbs as $crumb)
        {
            if ($crumb['title'] == Translation::get('ExternalRepositorys'))
                continue;
            $trail->add(new Breadcrumb($crumb['url'], $crumb['title']));
        }
        return $trail;
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
        return $renderer->toHtml();
    }

    public static function get_tree_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::TREE_NAME, true);
    }
}
