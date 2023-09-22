<?php
namespace Chamilo\Core\Lynx\Menu;

use Chamilo\Configuration\Package\PackageList;
use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

class PackageTypeMenu extends HtmlMenu
{
    protected ClassnameUtilities $classnameUtilities;

    private string $format;

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function __construct(
        PackageBundlesCacheService $packageBundlesCacheService, ClassnameUtilities $classnameUtilities,
        string $current_type, string $format
    )
    {
        $this->format = $format;
        $this->classnameUtilities = $classnameUtilities;

        parent::__construct([$this->get_items($packageBundlesCacheService->getAllPackages())]);

        $this->forceCurrentUrl($this->get_url($current_type));
    }

    public static function compare_items($item_one, $item_two): int
    {
        $item_one = strtolower($item_one['title']);
        $item_two = strtolower($item_two['title']);

        if ($item_one == $item_two)
        {
            return 0;
        }

        return ($item_one > $item_two) ? + 1 : - 1;
    }

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    private function get_items(PackageList $package_list): array
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
            usort($sub_items, ['\Chamilo\Core\Lynx\Menu\PackageTypeMenu', 'compare_items']);

            $item['sub'] = $sub_items;
        }

        return $item;
    }

    private function get_url(string $type): string
    {
        return (str_replace('__type__', $type, $this->format));
    }

    public function render_as_tree(): string
    {
        $renderer = new TreeMenuRenderer($this->getClassnameUtilities()->getClassnameFromNamespace(__CLASS__, true));
        $this->render($renderer, 'sitemap');

        return $renderer->toHtml();
    }
}
