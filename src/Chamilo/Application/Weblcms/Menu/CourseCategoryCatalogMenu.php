<?php
namespace Chamilo\Application\Weblcms\Menu;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\CollapsedTreeMenuRenderer;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuArrayRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use HTML_Menu;

/**
 * Displays course's categories in a tree.
 * Tree is collapsed by default.
 * 
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 * @package application.lib.weblcms.course
 */
class CourseCategoryCatalogMenu extends HtmlMenu
{
    const TREE_NAME = __CLASS__;

    public static function get_tree_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::TREE_NAME, true);
    }

    /**
     * The string passed to sprintf() to format category URLs
     */
    private $url_format;

    /**
     * If true display the number of items per category beside the category's title.
     * 
     * @var bool
     */
    private $display_child_count = true;

    /**
     * If true the menu has been initialized with a tree structure.
     * If false it has not been initialized.
     * 
     * @var bool
     */
    private $initialized = false;

    /**
     * Creates a new category navigation menu.
     * 
     * @param $url_format string The format to use for the URL of a category. Passed to sprintf(). Defaults to the
     *        string "?category=%s".
     * @param $extra_items array An array of extra tree items, added to the root.
     */
    public function __construct($current_category = '', $url_format = '?course_category=%s', $display_child_count = false)
    {
        parent::__construct();
        $this->url_format = $url_format;
        $this->display_child_count = $display_child_count;
        if ($current_category)
        {
            $this->forceCurrentUrl($this->get_category_url($current_category));
        }
    }

    /**
     * If true displays the number of children belonging to the category.
     * If false do not display the number of
     * children.
     * 
     * @return bool
     */
    public function get_display_child_count()
    {
        return $this->display_child_count;
    }

    public function set_display_child_count($value)
    {
        $this->display_child_count = $value;
    }

    public function is_initialized()
    {
        return $this->initialized;
    }

    public function init($extra_items = array())
    {
        $menu = $this->get_menu($extra_items);
        $this->setMenu($menu);
        $this->initialized = true;
    }

    /**
     * Returns the menu items.
     * 
     * @param $extra_items array An array of extra tree items, added to the root.
     * @return array An array with all menu items. The structure of this array is the structure needed by
     *         PEAR::HTML_Menu, on which this class is based.
     */
    public function get_menu($extra_items = array())
    {
        $usercategories = DataManager::retrieve_course_categories_ordered_by_name();
        
        $categories = array();
        while ($category = $usercategories->next_result())
        {
            $categories[$category->get_parent()][] = $category;
        }
        $result = $this->get_sub_menu_items($categories, 0);
        if (count($extra_items))
        {
            $result = array_merge($result, $extra_items);
        }
        return $result;
    }

    /**
     * Returns the items of the sub menu.
     * 
     * @param $categories array The categories to include in this menu.
     * @param $parent int The parent category ID.
     * @return array An array with all menu items. The structure of this array is the structure needed by
     *         PEAR::HTML_Menu, on which this class is based.
     */
    protected function get_sub_menu_items($categories, $parent)
    {
        $sub_tree = array();
        foreach ($categories[$parent] as $category)
        {
            $menu_item = array();
            
            if ($this->get_display_child_count())
            {
                $count = \Chamilo\Application\Weblcms\Course\Storage\DataManager::count(
                    Course::class_name(), 
                    new EqualityCondition(
                        new PropertyConditionVariable(Course::class_name(), Course::PROPERTY_CATEGORY), 
                        new StaticConditionVariable($category->get_id())));
                
                $count_text = " ($count)'";
            }
            else
            {
                $count_text = '';
            }
            $menu_item['title'] = $category->get_name() . $count_text;
            if (Request::get(Application::PARAM_ACTION) == Manager::ACTION_COURSE_CATEGORY_MANAGER)
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
    protected function get_category_url($category)
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        return htmlentities(sprintf($this->url_format, $category));
    }

    /**
     * Renders the menu as a tree
     * 
     * @return string The HTML formatted tree
     */
    public function render_as_tree()
    {
        if (! $this->is_initialized())
        {
            $this->init();
        }
        $renderer = new CollapsedTreeMenuRenderer($this->get_tree_name());
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }

    /**
     * Get the breadcrumbs which lead to the current category.
     * 
     * @return array The breadcrumbs.
     */
    public function get_breadcrumbs()
    {
        if (! $this->is_initialized())
        {
            $this->init();
        }
        
        $array_renderer = new HtmlMenuArrayRenderer();
        $this->render($array_renderer, 'urhere');
        $breadcrumbs = $array_renderer->toArray();
        foreach ($breadcrumbs as $crumb)
        {
            $crumb['name'] = $crumb['title'];
        }
        return $breadcrumbs;
    }
}
