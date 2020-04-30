<?php
namespace Chamilo\Core\Repository\Menu;

use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuArrayRenderer;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package repository.lib
 */

/**
 * This class provides a navigation menu to allow a user to browse through his categories of objects.
 *
 * @author Bart Mollet
 */
class ContentObjectCategoryMenu extends HtmlMenu
{
    const TREE_NAME = __CLASS__;

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    private $currentWorkspace;

    /**
     * The string passed to sprintf() to format category URLs
     */
    private $urlFmt;

    /**
     * The array renderer used to determine the breadcrumbs.
     */
    private $array_renderer;

    /**
     * Array to define the types on which the count on the categories should be filtered Leave empty if you want to
     * count everything
     *
     * @var String[]
     */
    private $filter_count_on_types;

    /**
     * Array to define the types on which the count on the categories should be excluded Leave empty if you want to
     * count everything
     *
     * @var String[]
     */
    private $exclude_types;

    /**
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $currentWorkspace
     * @param integer $current_category
     * @param string $url_format
     * @param array $extra_items
     * @param array $filter_count_on_types
     * @param array $exclude_types
     */
    public function __construct(
        WorkspaceInterface $currentWorkspace, $current_category = null, $url_format = '?category=%s',
        $extra_items = array(), $filter_count_on_types = array(), $exclude_types = array()
    )
    {
        $this->currentWorkspace = $currentWorkspace;
        $this->urlFmt = $url_format;

        $this->filter_count_on_types = $filter_count_on_types;
        $this->exclude_types = $exclude_types;

        $menu = $this->get_menu_items($extra_items);
        parent::__construct($menu);

        $this->forceCurrentUrl($this->get_category_url($current_category));
    }

    /**
     * @param int $parentId
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory[]
     */
    private function getCategories($parentId = 0)
    {
        if (!isset($this->categories))
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_TYPE_ID),
                new StaticConditionVariable($this->currentWorkspace->getId())
            );
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_TYPE),
                new StaticConditionVariable($this->currentWorkspace->getWorkspaceType())
            );
            $condition = new AndCondition($conditions);

            $contentObjectCategories = DataManager::retrieve_categories($condition);

            $this->categories = array();

            while ($contentObjectCategory = $contentObjectCategories->next_result())
            {
                $this->categories[$contentObjectCategory->get_parent()][] = $contentObjectCategory;
            }
        }

        return $this->categories[$parentId];
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\BreadcrumbTrail
     */
    public function get_breadcrumbs()
    {
        $trail = BreadcrumbTrail::getInstance();
        $htmlMenuArrayRenderer = new HtmlMenuArrayRenderer();

        $this->render($htmlMenuArrayRenderer, 'urhere');
        $breadcrumbs = $htmlMenuArrayRenderer->toArray();

        foreach ($breadcrumbs as $crumb)
        {
            $str = Translation::get('MyRepository');
            if (substr($crumb['title'], 0, strlen($str)) == $str)
            {
                continue;
            }
            $trail->add(new Breadcrumb($crumb['url'], substr($crumb['title'], 0, strpos($crumb['title'], '('))));
        }

        return $trail;
    }

    /**
     * Gets the URL of a given category
     *
     * @param $category int The id of the category
     *
     * @return string The requested URL
     */
    protected function get_category_url($category)
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        return htmlentities(sprintf($this->urlFmt, $category));
    }

    /**
     * Returns the menu items.
     *
     * @param $extra_items array An array of extra tree items, added to the root.
     *
     * @return array An array with all menu items. The structure of this array is the structure needed by
     *         PEAR::HTML_Menu, on which this class is based.
     */
    private function get_menu_items($extra_items)
    {
        $menu = array();
        $menu_item = array();

        $menu_item['title'] = $this->currentWorkspace->getTitle();
        $menu_item['url'] = $this->get_category_url(0);

        if (DataManager::workspace_has_categories($this->currentWorkspace))
        {
            $sub_menu_items = $this->get_sub_menu_items();
            if (count($sub_menu_items) > 0)
            {
                $menu_item['sub'] = $sub_menu_items;
            }
        }

        $glyph = new FontAwesomeGlyph('folder', array(), null, 'fas');

        $menu_item['class'] = $glyph->getClassNamesString();
        $menu_item[OptionsMenuRenderer::KEY_ID] = 0;
        $menu[0] = $menu_item;

        if (count($extra_items))
        {
            $menu = array_merge($menu, $extra_items);
        }

        return $menu;
    }

    /**
     * @param integer $parent
     *
     * @return string[][]
     */
    private function get_sub_menu_items($parent = 0)
    {
        $objects = $this->getCategories($parent);
        $categories = array();

        foreach ($objects as $category)
        {
            $menu_item = array();
            $menu_item['title'] = $category->get_name();
            $menu_item['url'] = $this->get_category_url($category->get_id());

            $sub_menu_items = $this->get_sub_menu_items($category->get_id());

            if (count($sub_menu_items) > 0)
            {
                $menu_item['sub'] = $sub_menu_items;
            }

            $glyph = new FontAwesomeGlyph('folder', array(), null, 'fas');

            $menu_item['class'] = $glyph->getClassNamesString();
            $menu_item[OptionsMenuRenderer::KEY_ID] = $category->get_id();
            $categories[$category->get_id()] = $menu_item;
        }

        return $categories;
    }

    /**
     * @return string
     */
    public static function get_tree_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::TREE_NAME, true);
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
}
