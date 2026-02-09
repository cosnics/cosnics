<?php
namespace Chamilo\Core\Menu\UserInterface\MenuRenderer;

use Chamilo\Core\Menu\Architecture\Domain\ItemRendererRegistry;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\UserInterface\Theme\Service\ThemePathBuilder;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Menu\UserInterface\MenuRenderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MenuRenderer
{
    protected array $brandPath;

    protected string $siteName;

    private ChamiloRequest $chamiloRequest;

    private CachedItemService $itemCacheService;

    private ItemRendererRegistry $itemRendererFactory;

    private ThemePathBuilder $themeWebPathBuilder;

    private WebPathBuilder $webPathBuilder;

    public function __construct(
        CachedItemService $itemCacheService, ItemRendererRegistry $itemRendererFactory, ChamiloRequest $chamiloRequest,
        WebPathBuilder $webPathBuilder, ThemePathBuilder $themeWebPathBuilder, string $siteName, array $brandPath = []
    )
    {
        $this->itemCacheService = $itemCacheService;
        $this->itemRendererFactory = $itemRendererFactory;
        $this->chamiloRequest = $chamiloRequest;
        $this->webPathBuilder = $webPathBuilder;
        $this->themeWebPathBuilder = $themeWebPathBuilder;
        $this->siteName = $siteName;
        $this->brandPath = $brandPath;
    }

    public function render(?User $user = null): string
    {
        $html = [];

        $numberOfItems = 0;
        $itemRenditions = [];

        if ($user instanceof User) {
            foreach ($this->findRootItems() as $item) {
                if (!$item->isHidden()) {
                    $itemRenderer = $this->getItemRendererFactory()->getItemRendererForItem($item);

                    $itemHtml = $itemRenderer->render($item, $user);

                    if (!empty($itemHtml)) {
                        $numberOfItems ++;
                        $itemRenditions[] = $itemHtml;
                    }
                }
            }
        }

        $html[] = $this->renderHeader($numberOfItems);
        $html[] = implode(PHP_EOL, $itemRenditions);
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     */
    public function findRootItems(): ArrayCollection
    {
        return $this->getItemCacheService()->findItemsByParentIdentifier('0');
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

    public function getSiteName(): string
    {
        return $this->siteName;
    }

    public function getThemeWebPathBuilder(): ThemePathBuilder
    {
        return $this->themeWebPathBuilder;
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }

    public function renderBrand(): string
    {
        $brandContext = $this->getBrandPath('context', 'Chamilo\Core\Admin');
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

    public function renderHeader(int $numberOfItems = 0): string
    {
        $html = [];

        $class = 'navbar navbar-static-top navbar-cosnics navbar-inverse';

        if ($numberOfItems == 0) {
            $class .= ' navbar-no-items';
        }

        $html[] = '<nav class="' . $class . '">';
        $html[] = '<div class="container-fluid">';
        $html[] = '<div class="navbar-header">';

        $html[] =
            '<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#menu-navbar-collapse" aria-expanded="false">';
        $html[] = '<span class="sr-only">Toggle navigation</span>';
        $html[] = '<span class="icon-bar"></span>';
        $html[] = '<span class="icon-bar"></span>';
        $html[] = '<span class="icon-bar"></span>';
        $html[] = '</button>';
        $html[] = $this->renderBrand();

        $html[] = '</div>';
        $html[] = '<div class="collapse navbar-collapse" id="menu-navbar-collapse">';
        $html[] = '<ul class="nav navbar-nav navbar-right">';

        return implode(PHP_EOL, $html);
    }
}