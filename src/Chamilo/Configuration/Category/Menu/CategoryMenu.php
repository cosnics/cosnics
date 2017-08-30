<?php
namespace Chamilo\Configuration\Category\Menu;

use Chamilo\Configuration\Category\Manager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuArrayRenderer;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * $Id: category_menu.class.php 191 2009-11-13 11:50:28Z chellee $
 * 
 * @package application.common.category_manager
 */
/**
 * This class provides a navigation menu to allow a user to browse through his reservations categories
 * 
 * @author Sven Vanpoucke
 */
class CategoryMenu extends HtmlMenu
{
    const TREE_NAME = __CLASS__;

    private $current_item;

    /**
     * The array renderer used to determine the breadcrumbs.
     */
    private $array_renderer;

    private $category_manager;

    /**
     * Creates a new category navigation menu.
     * 
     * @param int $owner The ID of the owner of the categories to provide in this menu.
     * @param int $current_category The ID of the current category in the menu.
     * @param string $url_format The format to use for the URL of a category. Passed to sprintf(). Defaults to the
     *        string "?category=%s".
     */
    public function __construct($current_item, $category_manager)
    {
        $this->current_item = $current_item;
        $this->category_manager = $category_manager;
        $menu = $this->get_menu();
        parent::__construct($menu);
        $this->array_renderer = new HtmlMenuArrayRenderer();
        $this->forceCurrentUrl($this->get_url($current_item));
    }

    public function get_menu()
    {
        $menu = array();
        
        $menu_item = array();
        $menu_item['title'] = Translation::get('Categories');
        $menu_item['url'] = $this->get_url();
        
        $sub_menu_items = $this->get_menu_items(0);
        if (count($sub_menu_items) > 0)
        {
            $menu_item['sub'] = $sub_menu_items;
        }
        
        $menu_item['class'] = 'type_category';
        $menu_item[OptionsMenuRenderer::KEY_ID] = 0;
        $menu[0] = $menu_item;
        return $menu;
    }

    /**
     * Returns the menu items.
     * 
     * @param array $extra_items An array of extra tree items, added to the root.
     * @return array An array with all menu items. The structure of this array is the structure needed by
     *         PEAR::HTML_Menu, on which this class is based.
     */
    private function get_menu_items($parent_id)
    {
        $category_class_name = get_class($this->category_manager->get_category());
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable($category_class_name::class_name(), $category_class_name::PROPERTY_PARENT), 
            new StaticConditionVariable($parent_id));
        
        $objects = $this->category_manager->retrieve_categories(
            $condition, 
            null, 
            null, 
            new OrderBy(
                new PropertyConditionVariable(
                    $category_class_name::class_name(), 
                    $category_class_name::PROPERTY_DISPLAY_ORDER)));
        
        while ($object = $objects->next_result())
        {
            if ($object)
            {
                $menu_item = array();
                $menu_item['title'] = $object->get_name();
                $menu_item['url'] = $this->get_url($object->get_id());
                
                $sub_menu_items = $this->get_menu_items($object->get_id());
                
                if (count($sub_menu_items) > 0)
                {
                    $menu_item['sub'] = $sub_menu_items;
                }
                
                $menu_item['class'] = 'type_category';
                $menu_item[OptionsMenuRenderer::KEY_ID] = $object->get_id();
                $menu[$object->get_id()] = $menu_item;
            }
        }
        
        return $menu;
    }

    private function get_url($id = null)
    {
        if (! $id)
            $id = 0;
        
        return $this->category_manager->get_url(array(Manager::PARAM_CATEGORY_ID => $id));
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
            if ($crumb['title'] == Translation::get('Categories'))
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
        return $renderer->toHTML();
    }

    public static function get_tree_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::TREE_NAME, true);
    }
}
