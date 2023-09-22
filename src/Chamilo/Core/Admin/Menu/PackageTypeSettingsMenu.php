<?php
namespace Chamilo\Core\Admin\Menu;

use Chamilo\Configuration\Package\PackageList;
use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;

class PackageTypeSettingsMenu extends HtmlMenu
{

    protected ClassnameUtilities $classnameUtilities;

    protected ConfigurationConsulter $configurationConsulter;

    protected string $format;

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function __construct(
        ClassnameUtilities $classnameUtilities, ConfigurationConsulter $configurationConsulter,
        PackageBundlesCacheService $packageBundlesCacheService, string $current_type, string $format
    )
    {
        $this->format = $format;
        $this->classnameUtilities = $classnameUtilities;
        $this->configurationConsulter = $configurationConsulter;

        parent::__construct(
            [
                $this->getItems(
                    $packageBundlesCacheService->getAllPackages()
                )
            ]
        );

        $this->forceCurrentUrl($this->getUrl($current_type));
    }

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    private function getItems(PackageList $packageList): ?array
    {
        $item = [];

        $item['class'] = $packageList->getTypeInlineGlyph()->getClassNamesString();
        $item['title'] = $packageList->getTypeName();
        $item['url'] = $this->getUrl($packageList->getType());
        $item[OptionsMenuRenderer::KEY_ID] = $packageList->getType();

        $sub_items = [];

        foreach ($packageList->getPackageLists() as $child)
        {
            $children = $this->getItems($child);
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
        $packages = $packageList->getPackages();

        foreach ($packages as $package)
        {
            if ($this->getConfigurationConsulter()->hasSettingsForContext($package->get_context()))
            {
                $has_settings = true;
                break;
            }
        }

        if ($has_settings || (count($sub_items) > 0))
        {
            if (!$has_settings)
            {
                $item['url'] = '#';
            }

            return $item;
        }

        return null;
    }

    private function getUrl($type): string
    {
        return (str_replace('__TYPE__', $type, $this->format));
    }

    public function render_as_tree(): string
    {
        $renderer = new TreeMenuRenderer($this->getClassnameUtilities()->getClassnameFromNamespace(__CLASS__, true));
        $this->render($renderer, 'sitemap');

        return $renderer->toHtml();
    }
}
