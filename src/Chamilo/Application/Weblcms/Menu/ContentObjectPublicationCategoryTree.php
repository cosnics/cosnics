<?php
namespace Chamilo\Application\Weblcms\Menu;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuArrayRenderer;

/**
 *
 * @package application.lib.weblcms.browser
 */
/**
 * A tree menu to display categories in a tool
 */
class ContentObjectPublicationCategoryTree extends HtmlMenu
{
    const TREE_NAME = __CLASS__;

    /**
     * The browser to which this category tree is associated
     */
    private $browser;

    /**
     * An id for this tree
     */
    private $tree_id;

    private $url_params;

    /**
     * Create a new category tree
     *
     * @param $browser PublicationBrowser The browser to associate this category tree with.
     * @param $tree_id string An id for the tree
     */
    public function __construct($browser, $tree_id, $url_params = array())
    {
        $this->browser = $browser;
        $this->tree_id = $tree_id;
        $this->url_params = $url_params;
        $menu = $this->get_menu_items();
        parent::__construct($menu);
        $this->forceCurrentUrl($this->get_category_url($tree_id));
    }

    public function as_html()
    {
        $renderer = new TreeMenuRenderer($this->get_tree_name());
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }

    public static function get_tree_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::TREE_NAME, true);
    }

    /**
     * Gets the current selected category id.
     *
     * @return int The current category id
     */
    public function get_current_category_id()
    {
        return intval(Request::get(Manager::PARAM_CATEGORY));
    }

    private function get_menu_items($extra_items = null)
    {
        $menu = array();
        $menu_item = array();
        $menu_item['title'] = Translation::get(
            (string) StringUtilities::getInstance()->createString($this->browser->get_tool_id()) . 'Title')->upperCamelize() . $this->get_category_count(
            0);
        $menu_item['url'] = $this->get_category_url(0);
        $sub_menu_items = $this->get_sub_menu_items(0);
        if (count($sub_menu_items) > 0)
        {
            $menu_item['sub'] = $sub_menu_items;
        }
        $menu_item['class'] = 'type_category';
        $menu_item[OptionsMenuRenderer::KEY_ID] = 0;
        $menu[0] = $menu_item;
        if (count($extra_items))
        {
            $menu = array_merge($menu, $extra_items);
        }

        return $menu;
    }

    private function get_sub_menu_items($parent)
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class_name(),
                ContentObjectPublicationCategory::PROPERTY_PARENT),
            new StaticConditionVariable($parent));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class_name(),
                ContentObjectPublicationCategory::PROPERTY_COURSE),
            new StaticConditionVariable($this->browser->get_parent()->get_course_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class_name(),
                ContentObjectPublicationCategory::PROPERTY_TOOL),
            new StaticConditionVariable($this->browser->get_parent()->get_tool_id()));
        $condition = new AndCondition($conditions);

        $objects = DataManager::retrieves(
            ContentObjectPublicationCategory::class_name(),
            new DataClassRetrievesParameters($condition));

        $categories = array();
        while ($category = $objects->next_result())
        {
            $menu_item = array();
            $menu_item['title'] = $category->get_name() . $this->get_category_count($category->get_id());
            $menu_item['url'] = $this->get_category_url($category->get_id());
            $sub_menu_items = $this->get_sub_menu_items($category->get_id());
            if (count($sub_menu_items) > 0)
            {
                $menu_item['sub'] = $sub_menu_items;
            }
            $menu_item['class'] = 'type_category';
            $menu_item[OptionsMenuRenderer::KEY_ID] = $category->get_id();
            $categories[$category->get_id()] = $menu_item;
        }
        return $categories;
    }

    private function get_category_count($category_id)
    {
        $count = $this->get_publication_count($category_id);
        return ($count > 0) ? ' (' . $count . ')' : '';
    }

    private function get_publication_count($category)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(),
                ContentObjectPublication::PROPERTY_COURSE_ID),
            new StaticConditionVariable($this->browser->get_parent()->get_course_id()));
        $conditions[] = $this->get_condition($category);

        $course_groups = $this->browser->get_course_groups();

        $course_group_ids = array();

        foreach ($course_groups as $course_group)
        {
            $course_group_ids[] = $course_group->get_id();
        }

        $subselect_condition = new InCondition(new StaticConditionVariable('type'), $this->browser->get_allowed_types());

        $conditions[] = new SubselectCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(),
                ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID),
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID),
            ContentObject::get_table_name(),
            $subselect_condition);

        $condition = new AndCondition($conditions);

        return DataManager::count_content_object_publications_with_view_right_granted_in_category_location(
            $this->browser->get_location(),
            $this->browser->get_entities(),
            $condition,
            $this->browser->get_user_id());
    }

    private function get_condition($category = null)
    {
        if (is_null($category))
        {
            $category = $this->get_current_category_id();
        }
        $tool_cond = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(),
                ContentObjectPublication::PROPERTY_TOOL),
            new StaticConditionVariable($this->browser->get_parent()->get_tool_id()));
        $category_cond = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(),
                ContentObjectPublication::PROPERTY_CATEGORY_ID),
            new StaticConditionVariable($category));
        return new AndCondition($tool_cond, $category_cond);
    }

    /**
     * Gets the URL of a category
     *
     * @param $category_id int The id of the category of which the URL is requested
     * @return string The URL
     */
    private function get_category_url($category_id)
    {
        $this->url_params[Manager::PARAM_CATEGORY] = $category_id;
        return $this->browser->get_url($this->url_params);
    }

    public function get_breadcrumbs()
    {
        $array_renderer = new HtmlMenuArrayRenderer();
        $this->render($array_renderer, 'urhere');
        $breadcrumbs = $array_renderer->toArray();
        foreach ($breadcrumbs as &$crumb)
        {
            $split = explode('(', $crumb['title']);
            $crumb['title'] = $split[0];
        }
        return $breadcrumbs;
    }
}
