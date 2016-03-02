<?php
namespace Chamilo\Core\Repository\Menu;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Selector\TypeSelectorFactory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use HTML_Menu;
use HTML_Menu_ArrayRenderer;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ObjectTypeMenu extends HTML_Menu
{
    const TREE_NAME = __CLASS__;

    /**
     * The string passed to sprintf() to format category URLs
     */
    private $type_format;

    private $category_format;

    /**
     * The array renderer used to determine the breadcrumbs.
     */
    private $array_renderer;

    /**
     *
     * @var RepositoryManager
     */
    private $repository_manager;

    /**
     *
     * @param RepositoryManager $repository_manager
     * @param string[] $selected_type
     * @param string $type_format
     * @param string $current_category
     * @param string $category_format
     */
    public function __construct(Manager $repository_manager, $selected_type = null, $type_format = '?category=%s',
        $current_category = null, $category_format = '?category=%s')
    {
        $this->type_format = $type_format;
        $this->category_format = $category_format;
        $this->repository_manager = $repository_manager;
        parent :: __construct($this->get_menu_items());
        $this->array_renderer = new HTML_Menu_ArrayRenderer();

        if (isset($selected_type))
        {
            $this->forceCurrentUrl($this->get_type_url($selected_type));
        }
        else
        {
            $this->forceCurrentUrl($this->get_category_url($current_category));
        }
    }

    /**
     * Returns the menu items.
     *
     * @param $extra_items array An array of extra tree items, added to the root.
     * @return array An array with all menu items. The structure of this array is the structure needed by
     *         PEAR::HTML_Menu, on which this class is based.
     */
    private function get_menu_items()
    {
        $menu = array();
        $menu_item = array();

        $typeSelectorFactory = new TypeSelectorFactory(DataManager :: get_registered_types());
        $type_selector = $typeSelectorFactory->getTypeSelector();

        foreach ($type_selector->get_categories() as $category)
        {
            $menu_item = array();

            $menu_item['class'] = 'category_' .
                 StringUtilities :: getInstance()->createString($category->get_type())->underscored();
            $menu_item['title'] = $category->get_name();
            $menu_item['url'] = $this->get_category_url($category->get_type());
            $menu_item[OptionsMenuRenderer :: KEY_ID] = $category;

            $sub_menu_items = array();

            foreach ($category->get_options() as $option)
            {
                $styleClassName = ClassnameUtilities :: getInstance()->getPackageNameFromNamespace(
                    $option->get_template_registration()->get_content_object_type());
                $styleClassName = StringUtilities :: getInstance()->createString($styleClassName)->underscored()->__toString();

                $sub_menu_item = array();
                $sub_menu_item['title'] = $option->get_label();
                $sub_menu_item['url'] = $this->get_type_url($option->get_template_registration_id());
                $sub_menu_item[OptionsMenuRenderer :: KEY_ID] = $option->get_template_registration_id();
                $sub_menu_item['class'] = 'type_' . $styleClassName;
                $sub_menu_items[] = $sub_menu_item;
            }

            $menu_item['sub'] = $sub_menu_items;

            $menu[] = $menu_item;
        }

        return $menu;
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
        return (str_replace('__CATEGORY__', $category, $this->category_format));
    }

    private function get_type_url($template_registration_id)
    {
        // TODO: Put another class in charge of the htmlentities() invocation
        return str_replace('__SELECTION__', $template_registration_id, $this->type_format);
    }

    /**
     * Get the breadcrumbs which lead to the current category.
     *
     * @return array The breadcrumbs.
     */
    public function get_breadcrumbs()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $this->render($this->array_renderer, 'urhere');
        $breadcrumbs = $this->array_renderer->toArray();
        foreach ($breadcrumbs as $crumb)
        {
            $str = Translation :: get('MyRepository');
            if (substr($crumb['title'], 0, strlen($str)) == $str)
                continue;
            $trail->add(new Breadcrumb($crumb['url'], substr($crumb['title'], 0, strpos($crumb['title'], '('))));
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

    /**
     *
     * @return string
     */
    public static function get_tree_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: TREE_NAME, true);
    }
}
