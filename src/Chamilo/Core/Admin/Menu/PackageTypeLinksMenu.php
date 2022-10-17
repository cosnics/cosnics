<?php
namespace Chamilo\Core\Admin\Menu;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Admin\Service\ActionProvider;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuArrayRenderer;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;

class PackageTypeLinksMenu extends HtmlMenu
{

    private ActionProvider $actionProvider;

    private $array_renderer;

    private $format;

    public function __construct(ActionProvider $actionProvider, $current_type, $format)
    {
        $this->actionProvider = $actionProvider;
        $this->format = $format;

        parent::__construct(
            array(
                $this->get_items(
                    PlatformPackageBundles::getInstance()->get_package_list()
                )
            )
        );

        $this->array_renderer = new HtmlMenuArrayRenderer();
        $this->forceCurrentUrl($this->get_url($current_type));
    }

    private function get_items(PackageList $package_list)
    {
        $item = [];

        $item['class'] = $package_list->get_type_icon()->getClassNamesString();
        $item['title'] = $package_list->getTypeName();
        $item['url'] = $this->get_url($package_list->getType());
        $item[OptionsMenuRenderer::KEY_ID] = $package_list->getType();

        $sub_items = [];

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

        $has_links = false;
        $packages = $package_list->get_packages();

        foreach ($packages as $package)
        {
            $registration = Configuration::registration($package->get_context());

            if (!empty($registration) && $registration[Registration::PROPERTY_STATUS])
            {
                if ($this->actionProvider->existsForContext($package->get_context()))
                {
                    $has_links = true;
                    break;
                }
            }
        }

        if ($has_links || (count($sub_items) > 0))
        {
            if (!$has_links)
            {
                $item['url'] = '';
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
        $renderer = new TreeMenuRenderer(ClassnameUtilities::getInstance()->getClassNameFromNamespace(__CLASS__, true));
        $this->render($renderer, 'sitemap');

        return $renderer->toHtml();
    }
}
