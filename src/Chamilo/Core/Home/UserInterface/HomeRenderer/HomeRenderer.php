<?php
namespace Chamilo\Core\Home\UserInterface\HomeRenderer;

use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Home\UserInterface\HomeRenderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class HomeRenderer
{
    protected HomeService $homeService;

    protected TabRenderer $tabRenderer;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    protected WebPathBuilder $webPathBuilder;

    public function __construct(
        HomeService $homeService, Translator $translator, UrlGenerator $urlGenerator, WebPathBuilder $webPathBuilder,
        TabRenderer $tabRenderer
    )
    {
        $this->homeService = $homeService;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->webPathBuilder = $webPathBuilder;
        $this->tabRenderer = $tabRenderer;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function render(?int $currentTabIdentifier = null, ?User $user = null): string
    {
        $html[] = $this->renderContent($currentTabIdentifier, $user);

        return implode(PHP_EOL, $html);
    }

    protected function getHomeService(): HomeService
    {
        return $this->homeService;
    }

    public function getTabRenderer(): TabRenderer
    {
        return $this->tabRenderer;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function renderContent(?int $currentTabIdentifier = null, ?User $user = null): string
    {
        $tabRenderer = $this->getTabRenderer();

        $html = [];

        $html[] = '<div class="portal-tabs">';

        $tabs = $this->getHomeService()->findElementsByTypeAndParentIdentifier(Element::TYPE_TAB);

        foreach ($tabs as $tabKey => $tab) {
            $html[] = $tabRenderer->render($tab, $tabKey, $currentTabIdentifier, $user);
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
