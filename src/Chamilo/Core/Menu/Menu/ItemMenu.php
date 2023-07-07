<?php
namespace Chamilo\Core\Menu\Menu;

use Chamilo\Core\Menu\Factory\ItemRendererFactory;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuArrayRenderer;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Menu
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemMenu extends HtmlMenu
{
    public const TREE_NAME = __CLASS__;

    protected ItemRendererFactory $itemRendererFactory;

    private ItemService $itemService;

    private Translator $translator;

    private string $urlFormat;

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function __construct(
        ItemRendererFactory $itemRendererFactory, ItemService $itemService, Translator $translator, string $urlFormat,
        string $currentParentIdentifier = '0'
    )
    {
        $this->itemRendererFactory = $itemRendererFactory;
        $this->itemService = $itemService;
        $this->translator = $translator;
        $this->urlFormat = $urlFormat;

        parent::__construct($this->getItems());

        $this->forceCurrentUrl($this->getCategoryUrl($currentParentIdentifier));
    }

    /**
     * @return string[]
     */
    public function getBreadcrumbs(): array
    {
        $arrayRenderer = new HtmlMenuArrayRenderer();

        $this->render($arrayRenderer, 'urhere');
        $breadcrumbs = $arrayRenderer->toArray();
        foreach ($breadcrumbs as $crumb)
        {
            $crumb['name'] = $crumb['title'];
            unset($crumb['title']);
        }

        return $breadcrumbs;
    }

    private function getCategoryUrl(string $itemIdentifier): string
    {
        return str_replace('__ITEM__', $itemIdentifier, $this->getUrlFormat());
    }

    public function getItemRendererFactory(): ItemRendererFactory
    {
        return $this->itemRendererFactory;
    }

    public function getItemService(): ItemService
    {
        return $this->itemService;
    }

    /**
     * @return string[][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    private function getItems(): array
    {
        $itemService = $this->getItemService();
        $items = $itemService->findRootCategoryItems();

        $subMenuItems = [];

        foreach ($items as $item)
        {
            $itemRenderer = $this->getItemRendererFactory()->getItemRenderer($item);

            $subMenuItem = [];

            $subMenuItem['title'] = $itemRenderer->renderTitleForCurrentLanguage($item);
            $subMenuItem['url'] = $this->getCategoryUrl($item->getId());

            $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');
            $subMenuItem['class'] = $glyph->getClassNamesString();

            $subMenuItem[OptionsMenuRenderer::KEY_ID] = $item->getId();

            $subMenuItems[] = $subMenuItem;
        }

        $menuItem = [];

        $menuItem['title'] = $this->getTranslator()->trans('Home', [], Manager::CONTEXT);
        $menuItem['url'] = $this->getCategoryUrl('0');

        $glyph = new FontAwesomeGlyph('home', [], null, 'fas');
        $menuItem['class'] = $glyph->getClassNamesString();

        $menuItem[OptionsMenuRenderer::KEY_ID] = 0;
        $menuItem['sub'] = $subMenuItems;

        return [$menuItem];
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlFormat(): string
    {
        return $this->urlFormat;
    }

    /**
     * @return string[]
     */
    public function renderAsList(): array
    {
        $renderer = new OptionsMenuRenderer();
        $this->render($renderer, 'sitemap');

        $rootName = $this->getTranslator()->trans('RootCategory', [], 'Chamilo\Core\Menu');

        return ['0' => $rootName] + $renderer->toArray();
    }

    public function renderAsTree(): string
    {
        $renderer = new TreeMenuRenderer('item-menu', '', '#', false);
        $this->render($renderer, 'sitemap');

        return $renderer->toHtml();
    }
}
