<?php
namespace Chamilo\Core\Admin\Menu;

use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use HTML_Menu;
use HTML_Menu_ArrayRenderer;

class PackageTypeSettingsMenu extends HTML_Menu
{

    private $format;

    private $array_renderer;

    public function __construct($current_type, $format)
    {
        $this->format = $format;

        parent :: __construct(
            array(
                $this->get_items(
                    \Chamilo\Configuration\Package\PlatformPackageBundles :: getInstance()->get_package_list())));

        $this->array_renderer = new HTML_Menu_ArrayRenderer();
        $this->forceCurrentUrl($this->get_url($current_type));
    }

    private function get_items(PackageList $package_list)
    {
        $item = array();
        $item['class'] = 'category';
        $item['title'] = $package_list->get_type_name();
        $item['url'] = $this->get_url($package_list->get_type());
        $item[OptionsMenuRenderer :: KEY_ID] = $package_list->get_type();

        $sub_items = array();

        foreach ($package_list->get_children() as $child)
        {
            $children = $this->get_items($child);
            if ($children)
            {
                $sub_items[] = $children;
            }
        }

        if (count($sub_items) > 0)
        {
            $item['sub'] = $sub_items;
        }

        $has_settings = false;
        $packages = $package_list->get_packages();
        foreach ($packages as $package)
        {
            if (\Chamilo\Configuration\Configuration :: get_instance()->has_settings($package->get_context()))
            {
                $has_settings = true;
                break;
            }
        }

        if ($has_settings || (count($sub_items) > 0))
        {
            if (! $has_settings)
            {
                unset($item['url']);
            }

            return $item;
        }

        return false;
    }

    private function get_url($type)
    {
        return (str_replace('__TYPE__', $type, $this->format));
    }

    public function render_as_tree()
    {
        $renderer = new TreeMenuRenderer(ClassnameUtilities :: getInstance()->getClassNameFromNamespace(__CLASS__, true));
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }
}
