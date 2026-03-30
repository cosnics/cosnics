<?php
namespace Chamilo\Core\Menu\UserInterface\MenuRenderer;

use Chamilo\Core\Menu\Architecture\Domain\ItemRendererRegistry;
use Chamilo\Core\Menu\Implementation\Menu\LinkItemRenderer;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException;
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
    public function __construct(
        protected CachedItemService $itemCacheService, protected ItemRendererRegistry $itemRendererFactory,
        protected ChamiloRequest $chamiloRequest, protected WebPathBuilder $webPathBuilder,
        protected ThemePathBuilder $themeWebPathBuilder, protected UrlGenerator $urlGenerator,
        protected Translator $translator, protected LinkItemRenderer $linkItemRenderer,
        protected SessionInterface $session, protected string $siteName, protected array $brandPath
    )
    {
    }

    public function render(?User $user = null): string
    {
        $html = [];

        $itemRenditions = [];

        $itemRenditions[] = $this->renderLoggedInAs($user);

        if ($user instanceof User) {
            foreach ($this->findRootItems() as $item) {
                if (!$item->isHidden()) {
                    try {
                        $itemRenderer = $this->itemRendererFactory->getItemRendererForItem($item);
                        $itemHtml = $itemRenderer->render($item, $user);

                        if (!empty($itemHtml)) {
                            $itemRenditions[] = $itemHtml;
                        }
                    }
                    catch (NoSuchClassException) {
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
        return $this->itemCacheService->findItemsByParentIdentifier(DataClass::EMPTY_UUID);
    }

    public function getBrandPath(?string $component = null, ?string $defaultValue = null): array|string
    {
        if ($component) {
            return $this->brandPath[$component] ?: $defaultValue;
        }

        return $this->brandPath;
    }

    protected function isLoggedInAs(): bool
    {
        return !is_null($this->session->get('_as_admin'));
    }

    public function renderBrand(): string
    {
        $brandContext = $this->getBrandPath('context', StringUtilities::LIBRARIES);
        $brandFilename = $this->getBrandPath('filename', 'LogoHeader');
        $brandExtension = $this->getBrandPath('extension', 'png');

        $brandWebPath = $this->themeWebPathBuilder->getImagePath($brandContext, $brandFilename, $brandExtension);

        $basePath = $this->webPathBuilder->getBasePath();

        return '<a class="navbar-brand" href="' . $basePath . '">' . '<img alt="' . $this->siteName . '" src="' .
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
        $html = [];

        if ($this->isLoggedInAs()) {
            $link = $this->urlGenerator->fromParameters([
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::LOGIN_AS->value
            ]);

            $linkItem = new Item();
            $linkItem->setDisplay(DisplayTypeEnum::ICON_AND_LABEL);
            $linkItem->setType(LinkItemRenderer::class);
            $linkItem->setIconClass('mask');
            $linkItem->setParentId(DataClass::EMPTY_UUID);
            $linkItem->setTitleForIsoCode(
                $this->translator->getLocale(), $this->translator->trans('Back', [], StringUtilities::LIBRARIES)
            );
            $linkItem->setSetting(LinkItemRenderer::CONFIGURATION_URL, $link);

            $html[] = $this->linkItemRenderer->render($linkItem, $user);
        }

        return implode(PHP_EOL, $html);
    }
}