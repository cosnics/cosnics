<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Admin\Menu\PackageTypeLinksMenu;
use Chamilo\Core\Admin\Service\ActionProvider;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Architecture\Interfaces\MenuComponent;
use Chamilo\Libraries\Format\Tabs\TabsRenderer;

/**
 * @package Chamilo\Core\Admin\Component
 */
class BrowserComponent extends Manager implements DelegateComponent, MenuComponent
{
    public const PARAM_TAB = 'tab';

    private string $currentTab;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageChamilo');

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->renderTabs();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getActionProvider(): ActionProvider
    {
        return $this->getService(ActionProvider::class);
    }

    /**
     * @return string
     */
    public function getCurrentTab(): string
    {
        if (!isset($this->currentTab))
        {
            $this->currentTab = $this->getRequest()->query->get(
                self::PARAM_TAB, $this->getClassnameUtilities()->getNamespaceId('Chamilo\Core')
            );
        }

        return $this->currentTab;
    }

    public function getPackageBundlesCacheService(): PackageBundlesCacheService
    {
        return $this->getService(PackageBundlesCacheService::class);
    }

    protected function getTabsRenderer(): TabsRenderer
    {
        return $this->getService(TabsRenderer::class);
    }

    /**
     * @throws \ReflectionException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function renderApplicationMenu(): string
    {
        $tabNamespace = $this->getClassnameUtilities()->getNamespaceFromId($this->getCurrentTab());
        $menu = new PackageTypeLinksMenu(
            $this->getClassnameUtilities(), $this->getPackageBundlesCacheService(), $this->getRegistrationConsulter(),
            $this->getActionProvider(), $tabNamespace, $this->get_url([self::PARAM_TAB => '__TYPE__'])
        );

        return $menu->render_as_tree();
    }

    /**
     * @throws \QuickformException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    protected function renderTabs(): string
    {
        $tabNamespace = ClassnameUtilities::getInstance()->getNamespaceFromId($this->getCurrentTab());
        $tabsCollection = $this->getActionProvider()->getTabsCollection($tabNamespace);
        $tabsCollection->sortByLabel();

        return $this->getTabsRenderer()->render('admin', $tabsCollection);
    }
}
