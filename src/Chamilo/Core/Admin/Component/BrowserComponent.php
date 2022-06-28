<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Admin\Menu\PackageTypeLinksMenu;
use Chamilo\Core\Admin\Service\ActionProvider;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Tabs\TabsRenderer;

class BrowserComponent extends Manager implements DelegateComponent
{
    public const PARAM_TAB = 'tab';

    private $currentTab;

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageChamilo');

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->renderTabs();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function getActionProvider(): ActionProvider
    {
        return $this->getService(ActionProvider::class);
    }

    /**
     * @return string
     */
    public function getCurrentTab()
    {
        if (!isset($this->currentTab))
        {
            $this->currentTab = $this->getRequest()->query->get(
                self::PARAM_TAB, ClassnameUtilities::getInstance()->getNamespaceId('Chamilo\Core')
            );
        }

        return $this->currentTab;
    }

    protected function getTabsRenderer(): TabsRenderer
    {
        return $this->getService(TabsRenderer::class);
    }

    /**
     * @return string
     */
    public function get_menu(): string
    {
        $tabNamespace = ClassnameUtilities::getInstance()->getNamespaceFromId($this->getCurrentTab());
        $menu = new PackageTypeLinksMenu(
            $this->getActionProvider(), $tabNamespace, $this->get_url(array(self::PARAM_TAB => '__TYPE__'))
        );

        return $menu->render_as_tree();
    }

    /**
     * @return bool
     */
    public function has_menu(): bool
    {
        return true;
    }

    protected function renderTabs(): string
    {
        $tabNamespace = ClassnameUtilities::getInstance()->getNamespaceFromId($this->getCurrentTab());
        $tabsCollection = $this->getActionProvider()->getTabsCollection($tabNamespace);
        $tabsCollection->sortByLabel();

        return $this->getTabsRenderer()->render('admin', $tabsCollection);
    }
}
