<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Core\Admin\Architecture\Domain\ActionProviderRegistry;
use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Admin\Service\PackageBundlesCacheService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Chamilo\Libraries\UserInterface\Tab\Service\TabsRenderer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Admin\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BrowseComponent extends Manager
{
    public const string PARAM_TAB = 'tab';

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        protected ActionProviderRegistry $actionProviderRegistry,
        protected PackageBundlesCacheService $packageBundlesCacheService, protected StringUtilities $stringUtilities,
        protected TabsRenderer $tabsRenderer
    )
    {
        parent::__construct($request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \QuickformException
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->renderHeader($currentUser);
        $html[] = $this->renderTabs();
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    /**
     * @return string
     */
    public function getCurrentTab(): string
    {
        $currentTab = $this->getRequest()->query->get(self::PARAM_TAB, Manager::CONTEXT);

        return $this->stringUtilities->createString($currentTab)->md5()->toString();
    }

    /**
     * @throws \QuickformException
     */
    protected function renderTabs(): string
    {
        $tabsCollection = $this->actionProviderRegistry->getTabsCollection();
        $tabsCollection->sortByLabel();

        return $this->tabsRenderer->renderNavigationAndContent('admin', $tabsCollection, $this->getCurrentTab());
    }
}
