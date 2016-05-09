<?php
namespace Chamilo\Application\Weblcms\Menu;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuArrayRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: course_category_menu.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.course
 */
/**
 * This class provides a navigation menu to allow a user to browse through categories of courses.
 *
 * @author Bart Mollet
 */
class CourseCategoryMenu extends HtmlMenu
{
    const TREE_NAME = __CLASS__;

    /**
     * The string passed to sprintf() to format category URLs
     */
    private $urlFmt;

    /**
     * The array renderer used to determine the breadcrumbs.
     */
    private $array_renderer;

    /**
     * Creates a new category navigation menu.
     *
     * @param $owner int The ID of the owner of the categories to provide in this menu.
     * @param $current_category int The ID of the current category in the menu.
     * @param $url_format string The format to use for the URL of a category. Passed to sprintf(). Defaults to the
     *        string "?category=%s".
     * @param $extra_items array An array of extra tree items, added to the root.
     */
    public function __construct($current_category, $url_format = '?category=%s', $extra_items = array())
    {
        $this->urlFmt = $url_format;
        $menu = $this->get_menu_items($extra_items);
        parent :: __construct($menu);
        $this->array_renderer = new HtmlMenuArrayRenderer();
        $this->forceCurrentUrl($this->get_category_url($current_category));
    }

    /**
     * Returns the menu items.
     *
     * @param $extra_items array An array of extra tree items, added to the root.
     * @return array An array with all menu items. The structure of this array is the structure needed by
     *         PEAR::HTML_Menu, on which this class is based.
     */
    private function get_menu_items($extra_items)
    {
        $usercategories = DataManager :: retrieve_course_categories_ordered_by_name();

        $categories = array();
        while ($category = $usercategories->next_result())
        {
            $categories[$category->get_parent()][] = $category;
        }
        $menu = $this->get_sub_menu_items($categories, 0);
        if (count($extra_items))
        {
            $menu = array_merge($menu, $extra_items);
        }

        $home = array();
        $home['title'] = Translation :: get('AllCourses');
        $home['url'] = $this->get_home_url(0);
        $home['class'] = 'home';
        $home_item[] = $home;
        $menu = array_merge($home_item, $menu);
        return $menu;
    }

    /**
     * Returns the items of the sub menu.
     *
     * @param $categories array The categories to include in this menu.
     * @param $parent int The parent category ID.
     * @return array An array with all menu items. The structure of this array is the structure needed by
     *         PEAR::HTML_Menu, on which this class is based.
     */
    private function get_sub_menu_items($categories, $parent)
    {
        $sub_tree = array();
        foreach ($categories[$parent] as $category)
        {
            $menu_item = array();

            $menu_item['title'] = $category->get_name();
            if (Request :: get(Application :: PARAM_ACTION) == Manager :: ACTION_COURSE_CATEGORY_MANAGER)
            {
                $menu_item['url'] = $this->get_category_url($category->get_id());
            }
            else
            {
                $menu_item['url'] = $this->get_category_url($category->get_id());
            }
            $sub_menu_items = $this->get_sub_menu_items($categories, $category->get_id());
            if (count($sub_menu_items) > 0)
            {
                $menu_item['sub'] = $sub_menu_items;
            }
            $menu_item['class'] = 'type_category';
            $menu_item['node_id'] = $category->get_id();
            $sub_tree[$category->get_id()] = $menu_item;
        }
        return $sub_tree;
    }

    /**
     * Gets the URL of a given category
     *
     * @param $category int The id of the category
     * @return string The requested URL
     */
    private function get_category_url($category)
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        return htmlentities(str_replace('__CATEGORY_ID__', $category, $this->urlFmt));
    }

    private function get_home_url($category)
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        return htmlentities(str_replace('&category_id=__CATEGORY_ID__', '', $this->urlFmt));
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
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: TREE_NAME, true);
    }
}
