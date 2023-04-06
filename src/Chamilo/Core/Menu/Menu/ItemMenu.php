<?php
namespace Chamilo\Core\Menu\Menu;

use Chamilo\Core\Menu\Storage\DataClass\CategoryItem;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuArrayRenderer;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Menu\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemMenu extends HtmlMenu
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

    public function __construct($current_category, $url_format = '?item=__ITEM__', $condition = null)
    {
        $this->urlFmt = $url_format;
        
        $menu = $this->get_items($condition);
        parent::__construct($menu);
        $this->array_renderer = new HtmlMenuArrayRenderer();
        $this->forceCurrentUrl($this->get_category_url($current_category));
    }

    private function get_items($condition)
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Item::class_name(), Item::PROPERTY_PARENT), 
            new StaticConditionVariable(0));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Item::class_name(), Item::PROPERTY_TYPE), 
            new StaticConditionVariable(CategoryItem::class_name()));
        $condition = new AndCondition($conditions);
        
        $parameters = new DataClassRetrievesParameters(
            $condition, 
            null, 
            null, 
            new OrderBy(new PropertyConditionVariable(Item::class_name(), Item::PROPERTY_SORT)));
        $items = DataManager::retrieves(Item::class_name(), $parameters);
        
        $menu_item = array();
        $menu_item['title'] = Translation::get('Home');
        $menu_item['url'] = $this->get_category_url(0);
        $menu_item['class'] = 'home';
        $menu_item[OptionsMenuRenderer::KEY_ID] = 0;
        
        $sub_menu_items = array();
        while ($item = $items->next_result())
        {
            $sub_menu_item = array();
            $sub_menu_item['title'] = $item->get_titles()->get_current_translation();
            $sub_menu_item['url'] = $this->get_category_url($item->get_id());
            $sub_menu_item['class'] = 'category';
            $sub_menu_item[OptionsMenuRenderer::KEY_ID] = $item->get_id();
            $sub_menu_items[] = $sub_menu_item;
        }
        
        $menu_item['sub'] = $sub_menu_items;
        $menu[] = $menu_item;
        return $menu;
    }

    /**
     * Gets the URL of a given category
     * 
     * @param int $category The id of the category
     * @return string The requested URL
     */
    private function get_category_url($item_id)
    {
        return str_replace('__ITEM__', $item_id, $this->urlFmt);
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

        /**
         * @var string[] $crumb
         */
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
        $renderer = new TreeMenuRenderer($this->get_tree_name(), '', '#', false);
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }

    public static function get_tree_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::TREE_NAME, true);
    }

    public function render_as_list()
    {
        $renderer = new OptionsMenuRenderer();
        $this->render($renderer, 'sitemap');
        $list = array('0' => Translation::get('RootCategory')) + $renderer->toArray();
        return $list;
    }
}
