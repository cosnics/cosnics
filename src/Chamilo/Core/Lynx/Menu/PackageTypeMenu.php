<?php
namespace Chamilo\Core\Lynx\Menu;

use Chamilo\Configuration\Package\PackageList;
use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuArrayRenderer;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

class PackageTypeMenu extends HtmlMenu
{

    private $format;

    private $array_renderer;

    public function __construct($current_type, $format)
    {
        $this->format = $format;
        
        parent::__construct(
            array(
                $this->get_items(
                    PlatformPackageBundles::getInstance()->get_package_list())));
        
        $this->array_renderer = new HtmlMenuArrayRenderer();
        $this->forceCurrentUrl($this->get_url($current_type));
    }

    private function get_items(PackageList $package_list)
    {
        $item = [];

        $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');

        $item['class'] = $glyph->getClassNamesString();
        $item['title'] = $package_list->getTypeName();
        $item['url'] = $this->get_url($package_list->getType());
        $item[OptionsMenuRenderer::KEY_ID] = $package_list->getType();
        
        $sub_items = [];
        
        foreach ($package_list->getPackageLists() as $child)
        {
            $sub_items[] = $this->get_items($child);
        }
        
        if (count($sub_items) > 0)
        {
            usort($sub_items, array('\Chamilo\Core\Lynx\PackageTypeMenu', 'compare_items'));
            
            $item['sub'] = $sub_items;
        }
        
        return $item;
    }

    public static function compare_items($item_one, $item_two)
    {
        $item_one = strtolower($item_one['title']);
        $item_two = strtolower($item_two['title']);
        
        if ($item_one == $item_two)
        {
            return 0;
        }
        
        return ($item_one > $item_two) ? + 1 : - 1;
    }

    private function get_url($type)
    {
        return (str_replace('__type__', $type, $this->format));
    }

    public function get_breadcrumbs()
    {
        $trail = BreadcrumbTrail::getInstance();
        // $this->render($this->array_renderer, 'urhere');
        // $breadcrumbs = $this->array_renderer->toArray();
        // foreach ($breadcrumbs as $crumb)
        // {
        // $str = Translation::get('MyRepository');
        // if (substr($crumb['title'], 0, strlen($str)) == $str)
        // continue;
        // $trail->add(new Breadcrumb($crumb['url'], substr($crumb['title'], 0, strpos($crumb['title'], '('))));
        // }
        return $trail;
    }

    public function render_as_tree()
    {
        $renderer = new TreeMenuRenderer(ClassnameUtilities::getInstance()->getClassNameFromNamespace(__CLASS__, true));
        $this->render($renderer, 'sitemap');
        return $renderer->toHtml();
    }
}
