<?php
namespace Chamilo\Core\Group\Menu;

use Chamilo\Core\Group\Ajax\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuArrayRenderer;

/**
 * $Id: group_menu.class.php 224 2009-11-13 14:40:30Z kariboe $
 *
 * @package group.lib
 */

/**
 * This class provides a navigation menu to allow a user to browse through categories of courses.
 *
 * @author Bart Mollet
 */
class GroupMenu extends HtmlMenu
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

    private $include_root;

    private $current_category;

    private $show_complete_tree;

    private $hide_current_category;

    /**
     * Creates a new category navigation menu.
     *
     * @param int $owner The ID of the owner of the categories to provide in this menu.
     * @param int $current_category The ID of the current category in the menu.
     * @param string $url_format The format to use for the URL of a category. Passed to sprintf(). Defaults to the
     *        string "?category=%s".
     * @param array $extra_items An array of extra tree items, added to the root.
     */
    public function __construct(
        $current_category, $url_format = '?application=group&go=browser&group_id=%s', $include_root = true,
        $show_complete_tree = false,
        $hide_current_category = false
    )
    {
        $this->include_root = $include_root;
        $this->show_complete_tree = $show_complete_tree;
        $this->hide_current_category = $hide_current_category;

        if ($current_category == '0' || is_null($current_category))
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_PARENT_ID),
                new StaticConditionVariable(0)
            );
            $group = DataManager::retrieves(
                Group::class_name(),
                new DataClassRetrievesParameters(
                    $condition,
                    1,
                    null,
                    new OrderBy(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME))
                )
            )->next_result();
            $this->current_category = $group;
        }
        else
        {
            $this->current_category = DataManager::retrieve(
                Group::class_name(),
                new DataClassRetrieveParameters(
                    new EqualityCondition(
                        new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_ID),
                        new StaticConditionVariable($current_category)
                    )
                )
            );
        }

        if (!$this->current_category instanceof Group)
        {
            throw new ObjectNotExistException(
                Translation::getInstance()->getTranslation('Group', null, 'Chamilo\Core\Group')
            );
        }

        $this->urlFmt = $url_format;
        $menu = $this->get_menu();
        parent::__construct($menu);
        $this->array_renderer = new HtmlMenuArrayRenderer();
        $this->forceCurrentUrl($this->get_url($this->current_category->get_id()));
    }

    public function get_menu()
    {
        $include_root = $this->include_root;

        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_PARENT_ID),
            new StaticConditionVariable(0)
        );
        $group = DataManager::retrieves(
            Group::class_name(),
            new DataClassRetrievesParameters(
                $condition,
                1,
                null,
                new OrderBy(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME))
            )
        )->next_result();
        if (!$include_root)
        {
            return $this->get_menu_items($group->get_id());
        }
        else
        {
            $menu = array();

            $menu_item = array();
            $menu_item['title'] = $group->get_name();
            // $menu_item['url'] = $this->get_url($group->get_id());
            $menu_item['url'] = $this->get_home_url();

            $sub_menu_items = $this->get_menu_items($group->get_id());
            if (count($sub_menu_items) > 0)
            {
                $menu_item['sub'] = $sub_menu_items;
            }

            $menu_item['class'] = 'home';
            $menu_item[OptionsMenuRenderer::KEY_ID] = $group->get_id();
            $menu[$group->get_id()] = $menu_item;

            return $menu;
        }
    }

    /**
     * Returns the menu items.
     *
     * @param array $extra_items An array of extra tree items, added to the root.
     *
     * @return array An array with all menu items. The structure of this array is the structure needed by
     *         PEAR::HTML_Menu, on which this class is based.
     */
    private function get_menu_items($parent_id = 0)
    {
        $current_category = $this->current_category;

        $show_complete_tree = $this->show_complete_tree;
        $hide_current_category = $this->hide_current_category;

        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_PARENT_ID),
            new StaticConditionVariable($parent_id)
        );
        $groups = DataManager::retrieves(
            Group::class_name(),
            new DataClassRetrievesParameters(
                $condition,
                null,
                null,
                new OrderBy(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME))
            )
        );

        while ($group = $groups->next_result())
        {
            $group_id = $group->get_id();

            if (!($group_id == $current_category->get_id() && $hide_current_category))
            {
                $menu_item = array();
                $menu_item['title'] = $group->get_name();
                $menu_item['url'] = $this->get_url($group->get_id());

                if ($group->is_parent_of($current_category) || $group->get_id() == $current_category->get_id() ||
                    $show_complete_tree
                )
                {
                    if ($group->has_children())
                    {
                        $menu_item['sub'] = $this->get_menu_items($group->get_id());
                    }
                }
                else
                {
                    if ($group->has_children())
                    {
                        $menu_item['children'] = 'expand';
                    }
                }

                $menu_item['class'] = 'category';
                $menu_item[OptionsMenuRenderer::KEY_ID] = $group->get_id();
                $menu[$group->get_id()] = $menu_item;
            }
        }

        return $menu;
    }

    /**
     * Gets the URL of a given category
     *
     * @param int $category The id of the category
     *
     * @return string The requested URL
     */
    public function get_url($group)
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        return htmlentities(sprintf($this->urlFmt, $group));
    }

    private function get_home_url($category)
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        return htmlentities(str_replace('&group_id=%s', '', $this->urlFmt));
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
        $redirect = new Redirect(
            array(Application::PARAM_CONTEXT => Manager::package(), Manager::PARAM_ACTION => 'xml_group_menu_feed')
        );

        $renderer = new TreeMenuRenderer($this->get_tree_name(), $redirect->getUrl(), $this->urlFmt);
        $this->render($renderer, 'sitemap');

        return $renderer->toHTML();
    }

    public static function get_tree_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::TREE_NAME, true);
    }
}
