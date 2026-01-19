<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Configuration\Service\PackageBundlesCacheService;
use Chamilo\Core\Admin\Architecture\Domain\ActionProviderCollection;
use Chamilo\Core\Admin\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Tabs\TabsRenderer;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Admin\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BrowserComponent extends Manager
{
    public const PARAM_TAB = 'tab';

    private string $currentTab;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     */
    public function run(): Response
    {
        if (!$this->getUser() instanceof User || !$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->renderTabs();
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    public function getActionProvider(): ActionProviderCollection
    {
        return $this->getService(ActionProviderCollection::class);
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
     * @throws \QuickformException
     */
    protected function renderTabs(): string
    {
        $tabsCollection = $this->getActionProvider()->getTabsCollection();
        $tabsCollection->sortByLabel();

        return $this->getTabsRenderer()->render('admin', $tabsCollection);
    }
}
