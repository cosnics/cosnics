<?php
namespace Chamilo\Core\Menu\UserInterface\MenuRenderer;

use Chamilo\Core\Menu\Architecture\Domain\ItemRendererRegistry;
use Chamilo\Core\Menu\Implementation\Menu\LinkItemRenderer;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\Theme\Service\ThemePathBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\UserInterface\MenuRenderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MenuRenderer
{
    protected array $brandPath;

    protected LinkItemRenderer $linkItemRenderer;

    protected string $siteName;

    private ChamiloRequest $chamiloRequest;

    private CachedItemService $itemCacheService;

    private ItemRendererRegistry $itemRendererFactory;

    private SessionInterface $session;

    private ThemePathBuilder $themeWebPathBuilder;

    private Translator $translator;

    private UrlGenerator $urlGenerator;

    private WebPathBuilder $webPathBuilder;

    public function __construct(
        CachedItemService $itemCacheService, ItemRendererRegistry $itemRendererFactory, ChamiloRequest $chamiloRequest,
        WebPathBuilder $webPathBuilder, ThemePathBuilder $themeWebPathBuilder, UrlGenerator $urlGenerator,
        Translator $translator, LinkItemRenderer $linkItemRenderer, SessionInterface $session, string $siteName,
        array $brandPath = []
    )
    {
        $this->itemCacheService = $itemCacheService;
        $this->itemRendererFactory = $itemRendererFactory;
        $this->chamiloRequest = $chamiloRequest;
        $this->webPathBuilder = $webPathBuilder;
        $this->themeWebPathBuilder = $themeWebPathBuilder;
        $this->siteName = $siteName;
        $this->brandPath = $brandPath;
        $this->urlGenerator = $urlGenerator;
        $this->session = $session;
        $this->translator = $translator;
        $this->linkItemRenderer = $linkItemRenderer;
    }

    public function render(?User $user = null): string
    {
        $html = [];

        $itemRenditions = [];

        $itemRenditions[] = $this->renderLoggedInAs($user);

        if ($user instanceof User) {
            foreach ($this->findRootItems() as $item) {
                if (!$item->isHidden()) {
                    $itemRenderer = $this->getItemRendererFactory()->getItemRendererForItem($item);
                    $itemHtml = $itemRenderer->render($item, $user);

                    if (!empty($itemHtml)) {
                        $itemRenditions[] = $itemHtml;
                    }
                }
            }
        }

        $html[] = $this->renderHeader();
        $html[] = implode(PHP_EOL, $itemRenditions);
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     */
    public function findRootItems(): ArrayCollection
    {
        return $this->getItemCacheService()->findItemsByParentIdentifier(DataClass::EMPTY_UUID);
    }

    public function getBrandPath(?string $component = null, ?string $defaultValue = null): array|string
    {
        if ($component) {
            return $this->brandPath[$component] ?: $defaultValue;
        }

        return $this->brandPath;
    }

    public function getChamiloRequest(): ChamiloRequest
    {
        return $this->chamiloRequest;
    }

    public function getItemCacheService(): CachedItemService
    {
        return $this->itemCacheService;
    }

    public function getItemRendererFactory(): ItemRendererRegistry
    {
        return $this->itemRendererFactory;
    }

    public function getLinkItemRenderer(): LinkItemRenderer
    {
        return $this->linkItemRenderer;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function getSiteName(): string
    {
        return $this->siteName;
    }

    public function getThemeWebPathBuilder(): ThemePathBuilder
    {
        return $this->themeWebPathBuilder;
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

    protected function isLoggedInAs(): bool
    {
        return !is_null($this->getSession()->get('_as_admin'));
    }

    public function renderBrand(): string
    {
        $brandContext = $this->getBrandPath('context', StringUtilities::LIBRARIES);
        $brandFilename = $this->getBrandPath('filename', 'LogoHeader');
        $brandExtension = $this->getBrandPath('extension', 'png');

        $brandWebPath = $this->getThemeWebPathBuilder()->getImagePath($brandContext, $brandFilename, $brandExtension);

        $basePath = $this->getWebPathBuilder()->getBasePath();

        return '<a class="navbar-brand" href="' . $basePath . '">' . '<img alt="' . $this->getSiteName() . '" src="' .
            $brandWebPath . '"></a>';
    }

    public function renderFooter(): string
    {
        $html = [];

        $html[] = '</ul>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</nav>';

        return implode(PHP_EOL, $html);
    }

    public function renderHeader(): string
    {
        $html = [];

        if ($this->isLoggedInAs()) {
            $colour = 'bg-danger';
        }
        else {
            $colour = 'bg-secondary';
        }

        $html[] = '<nav class="navbar navbar-expand-lg ' . $colour . '" data-bs-theme="dark">';
        $html[] = '<div class="container-xxl">';
        $html[] = '<div class="navbar-header">';

        $html[] = $this->renderBrand();

        $html[] =
            '<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu-navbar-collapse" aria-controls="menu-navbar-collapse" aria-expanded="false" aria-label="Toggle navigation">';
        $html[] = '<span class="navbar-toggler-icon"></span>';
        $html[] = '</button>';

        $html[] = '</div>';
        $html[] = '<div class="collapse navbar-collapse" id="menu-navbar-collapse">';
        $html[] = '<ul class="navbar-nav ms-auto mb-2 mb-lg-0">';

        return implode(PHP_EOL, $html);
    }

    protected function renderLoggedInAs(?User $user = null): string
    {
        $translator = $this->getTranslator();

        $html = [];

        if ($this->isLoggedInAs()) {
            $link = $this->getUrlGenerator()->fromParameters([
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => ActionEnum::LOGIN_AS->value
            ]);

            $linkItem = new Item();
            $linkItem->setDisplay(DisplayTypeEnum::ICON_AND_LABEL);
            $linkItem->setType(LinkItemRenderer::class);
            $linkItem->setIconClass('mask');
            $linkItem->setParentId(DataClass::EMPTY_UUID);
            $linkItem->setTitleForIsoCode(
                $translator->getLocale(), $translator->trans('Back', [], StringUtilities::LIBRARIES)
            );
            $linkItem->setSetting(LinkItemRenderer::CONFIGURATION_URL, $link);

            $html[] = $this->getLinkItemRenderer()->render($linkItem, $user);
        }

        return implode(PHP_EOL, $html);
    }
}