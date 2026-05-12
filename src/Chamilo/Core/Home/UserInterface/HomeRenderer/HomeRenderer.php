<?php
namespace Chamilo\Core\Home\UserInterface\HomeRenderer;

use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Home\UserInterface\HomeRenderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
readonly class HomeRenderer
{
    public function __construct(
        protected HomeService $homeService, protected Translator $translator, protected UrlGenerator $urlGenerator,
        protected WebPathBuilder $webPathBuilder, protected TabRenderer $tabRenderer
    )
    {
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

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function renderContent(?int $currentTabIdentifier = null, ?User $user = null): string
    {
        $html = [];

        $html[] = '<div class="portal-tabs">';

        $tabs = $this->homeService->findElementsByTypeAndParentIdentifier(Element::TYPE_TAB);

        foreach ($tabs as $tabKey => $tab) {
            $html[] = $this->tabRenderer->render($tab, $tabKey, $currentTabIdentifier, $user);
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
