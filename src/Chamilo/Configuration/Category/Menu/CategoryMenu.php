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
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
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

    /**
     * The array renderer used to determine the breadcrumbs.
     */
    private $array_renderer;

    private $category_manager;

    private $current_item;

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
            {
                continue;
            }
            $trail->add(new Breadcrumb($crumb['url'], $crumb['title']));
        }

        return $trail;
    }

    public function get_menu()
    {
        $menu = [];

        $menu_item = [];
        $menu_item['title'] = Translation::get('Categories');
        $menu_item['url'] = $this->get_url();

        $sub_menu_items = $this->get_menu_items(0);
        if (count($sub_menu_items) > 0)
        {
            $menu_item['sub'] = $sub_menu_items;
        }

        $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');

        $menu_item['class'] = $glyph->getClassNamesString();

        $menu_item[OptionsMenuRenderer::KEY_ID] = 0;
        $menu[0] = $menu_item;

        return $menu;
    }

    /**
     * Returns the menu items.
     *
     * @param array $extra_items An array of extra tree items, added to the root.
     *
     * @return array An array with all menu items. The structure of this array is the structure needed by
     *         PEAR::HTML_Menu, on which this class is based.
     */
    private function get_menu_items($parent_id)
    {
        $category_class_name = get_class($this->category_manager->getCategory());

        $condition = new EqualityCondition(
            new PropertyConditionVariable($category_class_name, $category_class_name::PROPERTY_PARENT),
            new StaticConditionVariable($parent_id)
        );

        $objects = $this->category_manager->retrieve_categories(
            $condition, null, null, new OrderBy(array(
                    new OrderProperty(
                        new PropertyConditionVariable(
                            $category_class_name, $category_class_name::PROPERTY_DISPLAY_ORDER
                        )
                    )
                ))
        );

        foreach ($objects as $object)
        {
            if ($object)
            {
                $menu_item = [];
                $menu_item['title'] = $object->get_name();
                $menu_item['url'] = $this->get_url($object->get_id());

                $sub_menu_items = $this->get_menu_items($object->get_id());

                if (count($sub_menu_items) > 0)
                {
                    $menu_item['sub'] = $sub_menu_items;
                }

                $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');

                $menu_item['class'] = $glyph->getClassNamesString();
                $menu_item[OptionsMenuRenderer::KEY_ID] = $object->get_id();
                $menu[$object->get_id()] = $menu_item;
            }
        }

        return $menu;
    }

    public static function get_tree_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::TREE_NAME, true);
    }

    private function get_url($id = null)
    {
        if (!$id)
        {
            $id = 0;
        }

        return $this->category_manager->get_url(array(Manager::PARAM_CATEGORY_ID => $id));
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
}
