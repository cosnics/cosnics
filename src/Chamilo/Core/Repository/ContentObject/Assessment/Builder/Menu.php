<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder;

use Chamilo\Core\Repository\Component\BuilderComponent;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuArrayRenderer;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package repository.lib.complex_builder
 */

/**
 * This class provides a navigation menu to allow a user to browse through his categories of objects.
 *
 * @author Sven Vanpoucke
 */
class Menu extends HtmlMenu
{
    public const TREE_NAME = __CLASS__;

    /**
     * The array renderer used to determine the breadcrumbs.
     */
    private $array_renderer;

    private $cloi;

    private $root;

    private $show_url;

    /**
     * The string passed to sprintf() to format category URLs
     */
    private $urlFmt;

    /*
     * Boolean to determine wheter the url should be added or not @var Bool
     */

    /**
     * Boolean to determine wheter the nodes of the tree which are not complex are shown in the tree or not
     */
    private $view_entire_structure;

    /**
     * Creates a new category navigation menu.
     *
     * @param $owner            int The ID of the owner of the categories to provide in this menu.
     * @param $current_category int The ID of the current category in the menu.
     * @param $url_format       string The format to use for the URL of a category. Passed to sprintf(). Defaults to the
     *                          string "?category=%s".
     * @param $extra_items      array An array of extra tree items, added to the root.
     */
    public function __construct(
        $root, $cloi, $url_format = '?application=repository&go=build_complex&builder_action=browse',
        $view_entire_structure = false, $show_url = true
    )
    {
        $url_format .= '&cloi=__CLOI__';
        $this->view_entire_structure = $view_entire_structure;
        $extra = ['publish'];

        foreach ($extra as $item)
        {
            if (Request::get($item))
            {
                $url_format .= '&' . $item . '=' . Request::get($item);
            }
        }

        $this->show_url = $show_url;
        $this->cloi = $cloi;
        $this->root = $root;
        $this->urlFmt = $url_format;

        $menu = $this->get_menu($root);
        parent::__construct($menu);
        $this->array_renderer = new HtmlMenuArrayRenderer();
        $this->forceCurrentUrl($this->get_cloi_url($cloi));
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
        array_shift($breadcrumbs);
        $trail = BreadcrumbTrail::getInstance();
        foreach ($breadcrumbs as $crumb)
        {
            $trail->add(new Breadcrumb($crumb['url'], $crumb['title']));
        }

        return $trail;
    }

    private function get_build_complex_url($object)
    {
        return Path::getInstance()->getBasePath(true) . 'index.php?' . Application::PARAM_CONTEXT . '=' .
            \Chamilo\Core\Repository\Manager::CONTEXT . '&' . Application::PARAM_ACTION . '=' .
            \Chamilo\Core\Repository\Manager::ACTION_BUILD_COMPLEX_CONTENT_OBJECT . '&' .
            \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID . '=' . $object->get_id() . '&' .
            BuilderComponent::PARAM_POPUP . '=1';
    }

    private function get_cloi_url($cloi = null)
    {
        if ($cloi == null || $cloi->get_ref() == $this->root)
        {
            return str_replace('&cloi=__CLOI__', '', $this->urlFmt);
        }

        return str_replace('__CLOI__', $cloi->get_id(), $this->urlFmt);
    }

    public function get_menu($root)
    {
        $menu = [];
        $lo = $root;
        $menu_item = [];
        $menu_item['title'] = $lo->get_title();

        if ($this->show_url)
        {
            $menu_item['url'] = $this->get_cloi_url();
        }

        $sub_menu_items = $this->get_menu_items($root->get_id());
        if (count($sub_menu_items) > 0)
        {
            $menu_item['sub'] = $sub_menu_items;
        }

        $ident = new NamespaceIdentGlyph($lo::CONTEXT);
        $menu_item['class'] = $ident->getClassNamesString();

        $menu_item[OptionsMenuRenderer::KEY_ID] = 0;
        $menu[0] = $menu_item;

        return $menu;
    }

    /**
     * Returns the menu items.
     *
     * @param $extra_items array An array of extra tree items, added to the root.
     *
     * @return array An array with all menu items. The structure of this array is the structure needed by
     *         PEAR::HTML_Menu, on which this class is based.
     */
    private function get_menu_items($parent_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
            ), new StaticConditionVariable($parent_id), ComplexContentObjectItem::getStorageUnitName()
        );
        $parameters = new DataClassRetrievesParameters($condition);
        $clois = DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class, $parameters
        );

        foreach ($clois as $cloi)
        {
            $lo = DataManager::retrieve_by_id(
                ContentObject::class, $cloi->get_ref()
            );
            $url = null;

            if (in_array($lo->getType(), DataManager::get_active_helper_types()))
            {
                $lo = DataManager::retrieve_by_id(
                    ContentObject::class, $lo->get_reference()
                );
                $url = $this->get_build_complex_url($lo);
            }

            if ($lo instanceof ComplexContentObjectSupport || $this->view_entire_structure)
            {
                $menu_item = [];
                $menu_item['title'] = $lo->get_title();

                if ($this->show_url)
                {
                    if ($url)
                    {
                        $menu_item['onclick'] = 'javascript:openPopup(\'' . addslashes($url) . '\'); return false;';
                    }
                    else
                    {
                        $menu_item['url'] = $this->get_cloi_url($cloi);
                    }
                }

                $sub_menu_items = $this->get_menu_items($cloi->get_ref());
                if (count($sub_menu_items) > 0)
                {
                    $menu_item['sub'] = $sub_menu_items;
                }

                $ident = new NamespaceIdentGlyph($lo::CONTEXT);
                $menu_item['class'] = $ident->getClassNamesString();

                $menu_item[OptionsMenuRenderer::KEY_ID] = $cloi->get_id();
                $menu[$cloi->get_id()] = $menu_item;
            }
        }

        return $menu;
    }

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

        return $renderer->toHtml();
    }
}
