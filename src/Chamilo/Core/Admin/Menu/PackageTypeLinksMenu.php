<?php
namespace Chamilo\Core\Admin\Menu;

use Chamilo\Configuration\Package\PackageList;
use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Admin\Service\ActionProvider;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;

class PackageTypeLinksMenu extends HtmlMenu
{

    protected ClassnameUtilities $classnameUtilities;

    protected RegistrationConsulter $registrationConsulter;

    private ActionProvider $actionProvider;

    private string $format;

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function __construct(
        ClassnameUtilities $classnameUtilities, PackageBundlesCacheService $packageBundlesCacheService,
        RegistrationConsulter $registrationConsulter, ActionProvider $actionProvider, string $currentType,
        string $format
    )
    {
        $this->actionProvider = $actionProvider;
        $this->format = $format;
        $this->registrationConsulter = $registrationConsulter;
        $this->classnameUtilities = $classnameUtilities;

        parent::__construct(
            [
                $this->getItems($packageBundlesCacheService->getAllPackages())
            ]
        );

        $this->forceCurrentUrl($this->getUrl($currentType));
    }

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
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

        $has_links = false;
        $packages = $packageList->getPackages();

        foreach ($packages as $package)
        {
            $registration = $this->getRegistrationConsulter()->getRegistrationForContext($package->get_context());

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

        return null;
    }

    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->registrationConsulter;
    }

    private function getUrl(string $type): string
    {
        return (str_replace('__TYPE__', $type, $this->format));
    }

    /**
     * @throws \ReflectionException
     */
    public function render_as_tree(): string
    {
        $renderer = new TreeMenuRenderer($this->getClassnameUtilities()->getClassnameFromNamespace(__CLASS__, true));
        $this->render($renderer, 'sitemap');

        return $renderer->toHtml();
    }
}
