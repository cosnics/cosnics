<?php
namespace Chamilo\Core\Menu\Service\Renderer;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Menu\Factory\ItemRendererFactory;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Service\RightsCacheService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Menu\Service\Renderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MenuRenderer
{
    private ChamiloRequest $chamiloRequest;

    private ConfigurationConsulter $configurationConsulter;

    private CachedItemService $itemCacheService;

    private ItemRendererFactory $itemRendererFactory;

    private RightsCacheService $rightsCacheService;

    private ThemePathBuilder $themeWebPathBuilder;

    private WebPathBuilder $webPathBuilder;

    public function __construct(
        CachedItemService $itemCacheService, RightsCacheService $rightsCacheService,
        ItemRendererFactory $itemRendererFactory, ChamiloRequest $chamiloRequest,
        ConfigurationConsulter $configurationConsulter, WebPathBuilder $webPathBuilder,
        ThemePathBuilder $themeWebPathBuilder
    )
    {
        $this->itemCacheService = $itemCacheService;
        $this->rightsCacheService = $rightsCacheService;
        $this->itemRendererFactory = $itemRendererFactory;
        $this->chamiloRequest = $chamiloRequest;
        $this->configurationConsulter = $configurationConsulter;
        $this->webPathBuilder = $webPathBuilder;
        $this->themeWebPathBuilder = $themeWebPathBuilder;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function render(string $containerMode = 'container-fluid', ?User $user = null): string
    {
        $html = [];

        $numberOfItems = 0;
        $itemRenditions = [];

        if ($user instanceof User)
        {
            foreach ($this->findRootItems() as $item)
            {
                if ($this->getRightsCacheService()->canUserViewItem($user, $item))
                {
                    if (!$item->isHidden())
                    {
                        $itemRenderer = $this->getItemRendererFactory()->getItemRenderer($item);

                        $itemHtml = $itemRenderer->render($item, $user);

                        if (!empty($itemHtml))
                        {
                            $numberOfItems ++;
                            $itemRenditions[] = $itemHtml;
                        }
                    }
                }
            }
        }

        $html[] = $this->renderHeader($containerMode, $numberOfItems);
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

    public function getChamiloRequest(): ChamiloRequest
    {
        return $this->chamiloRequest;
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getItemCacheService(): CachedItemService
    {
        return $this->itemCacheService;
    }

    public function getItemRendererFactory(): ItemRendererFactory
    {
        return $this->itemRendererFactory;
    }

    public function getRightsCacheService(): RightsCacheService
    {
        return $this->rightsCacheService;
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
        $configurationConsulter = $this->getConfigurationConsulter();

        $siteName = $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'site_name']);
        $brandImage = $configurationConsulter->getSetting(['Chamilo\Core\Menu', 'brand_image']);

        if ($brandImage)
        {
            $brandSource = $brandImage;
        }
        else
        {
            $brandSource = $this->getThemeWebPathBuilder()->getImagePath('Chamilo\Configuration', 'LogoHeader');
        }

        $basePath = $this->getWebPathBuilder()->getBasePath();

        return '<a class="navbar-brand" href="' . $basePath . '">' . '<img alt="' . $siteName . '" src="' .
            $brandSource . '"></a>';
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

    public function renderHeader(string $containerMode, int $numberOfItems = 0): string
    {
        $html = [];

        $class = 'navbar navbar-static-top navbar-cosnics navbar-inverse';

        if ($numberOfItems == 0)
        {
            $class .= ' navbar-no-items';
        }

        $html[] = '<nav class="' . $class . '">';
        $html[] = '<div class="' . $containerMode . '">';
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