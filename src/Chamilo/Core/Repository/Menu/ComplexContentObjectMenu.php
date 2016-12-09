<?php
namespace Chamilo\Core\Repository\Menu;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuArrayRenderer;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * $Id: complex_content_object_menu.class.php 204 2009-11-13 12:51:30Z kariboe $
 * 
 * @package repository.lib
 */
/**
 * This class provides a navigation menu to allow a user to browse through his categories of objects.
 * 
 * @author Sven Vanpoucke
 */
class ComplexContentObjectMenu extends HtmlMenu
{
    const TREE_NAME = __CLASS__;

    private $current_item;

    private $root;

    /**
     * The string passed to sprintf() to format category URLs
     */
    private $urlFmt;

    /**
     * The array renderer used to determine the breadcrumbs.
     */
    private $array_renderer;

    /**
     * Boolean to determine wheter the nodes of the tree which are not complex are shown in the tree or not
     */
    private $view_entire_structure;

    private $dm;

    /**
     * Creates a new category navigation menu.
     * 
     * @param $owner int The ID of the owner of the categories to provide in this menu.
     * @param $current_category int The ID of the current category in the menu.
     * @param $url_format string The format to use for the URL of a category. Passed to sprintf(). Defaults to the
     *        string "?category=%s".
     * @param $extra_items array An array of extra tree items, added to the root.
     */
    public function __construct($root, $current_item, $url_format = '?go=browsecomplex&cloi_id=%s&cloi_root_id=%s', $view_entire_structure = false)
    {
        $this->view_entire_structure = $view_entire_structure;
        $extra = array('publish', 'clo_action');
        
        foreach ($extra as $item)
        {
            if (Request::get($item))
                $url_format .= '&' . $item . '=' . Request::get($item);
        }
        
        $this->current_item = $current_item;
        $this->root = $root;
        $this->urlFmt = $url_format;
        $menu = $this->get_menu($root);
        parent::__construct($menu);
        $this->array_renderer = new HtmlMenuArrayRenderer();
        $this->forceCurrentUrl($this->get_cloi_url($current_item));
    }

    public function get_menu($root)
    {
        $menu = array();
        $lo = DataManager::retrieve_by_id(ContentObject::class_name(), $root);
        $menu_item = array();
        $menu_item['title'] = $lo->get_title();
        $menu_item['url'] = $this->get_cloi_url($root);
        
        $sub_menu_items = $this->get_menu_items($root);
        if (count($sub_menu_items) > 0)
        {
            $menu_item['sub'] = $sub_menu_items;
        }
        
        $menu_item['class'] = 'type_' . $lo->get_type();
        // $menu_item['class'] = 'type_category';
        $menu_item[OptionsMenuRenderer::KEY_ID] = $root;
        $menu[$root] = $menu_item;
        return $menu;
    }

    /**
     * Returns the menu items.
     * 
     * @param $extra_items array An array of extra tree items, added to the root.
     * @return array An array with all menu items. The structure of this array is the structure needed by
     *         PEAR::HTML_Menu, on which this class is based.
     */
    private function get_menu_items($cloi)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), ComplexContentObjectItem::PROPERTY_PARENT), 
            new StaticConditionVariable($cloi));
        $objects = DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class_name(), 
            $condition);
        
        while ($object = $objects->next_result())
        {
            if ($object->is_complex() || $this->view_entire_structure)
            {
                $lo = DataManager::retrieve_by_id(ContentObject::class_name(), $object->get_ref());
                $menu_item = array();
                $menu_item['title'] = $lo->get_title();
                $menu_item['url'] = $this->get_cloi_url($object->get_ref());
                
                $sub_menu_items = $this->get_menu_items($object->get_ref());
                if (count($sub_menu_items) > 0)
                {
                    $menu_item['sub'] = $sub_menu_items;
                }
                
                $menu_item['class'] = 'type_' . $lo->get_type();
                // $menu_item['class'] = 'type_category';
                $menu_item[OptionsMenuRenderer::KEY_ID] = $object->get_ref();
                $menu[$object->get_ref()] = $menu_item;
            }
        }
        
        return $menu;
    }

    private function get_cloi_url($cloi_id)
    {
        return htmlentities(sprintf($this->urlFmt, $cloi_id, $this->root));
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
        foreach ($breadcrumbs as $crumb)
        {
            $crumb['name'] = $crumb['title'];
            unset($crumb['title']);
        }
        return $breadcrumbs;
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
