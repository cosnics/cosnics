<?php
namespace Chamilo\Core\Menu\Menu;

use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuArrayRenderer;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Core\Menu\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemMenu extends HtmlMenu
{
    const TREE_NAME = __CLASS__;

    /**
     * @var \Chamilo\Core\Menu\Service\ItemService
     */
    private $itemService;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * The string passed to sprintf() to format category URLs
     * @var string
     */
    private $urlFormat;

    /**
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     * @param \Symfony\Component\Translation\Translator $translator
     * @param string $urlFormat
     * @param int $currentParentIdentifier
     */
    public function __construct(
        ItemService $itemService, Translator $translator, string $urlFormat, int $currentParentIdentifier = 0
    )
    {
        $this->itemService = $itemService;
        $this->translator = $translator;
        $this->urlFormat = $urlFormat;

        parent::__construct($this->getItems());

        $this->forceCurrentUrl($this->getCategoryUrl($currentParentIdentifier));
    }

    /**
     * Get the breadcrumbs which lead to the current category.
     *
     * @return string[]
     */
    public function getBreadcrumbs()
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

    /**
     * Gets the URL of a given category
     *
     * @param integer $itemIdentifier The id of the category
     *
     * @return string The requested URL
     */
    private function getCategoryUrl($itemIdentifier)
    {
        return str_replace('__ITEM__', $itemIdentifier, $this->getUrlFormat());
    }

    /**
     * @return \Chamilo\Core\Menu\Service\ItemService
     */
    public function getItemService(): ItemService
    {
        return $this->itemService;
    }

    /**
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     */
    public function setItemService(ItemService $itemService): void
    {
        $this->itemService = $itemService;
    }

    /**
     * @return string[][]
     */
    private function getItems()
    {
        $itemService = $this->getItemService();
        $items = $itemService->findRootCategoryItems();

        $subMenuItems = [];

        foreach ($items as $item)
        {
            $subMenuItem = [];

            $subMenuItem['title'] = $itemService->getItemTitleForCurrentLanguage($item);
            $subMenuItem['url'] = $this->getCategoryUrl($item->getId());

            $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');
            $subMenuItem['class'] = $glyph->getClassNamesString();

            $subMenuItem[OptionsMenuRenderer::KEY_ID] = $item->getId();

            $subMenuItems[] = $subMenuItem;
        }

        $menuItem = [];

        $menuItem['title'] = $this->getTranslator()->trans('Home');
        $menuItem['url'] = $this->getCategoryUrl(0);

        $glyph = new FontAwesomeGlyph('home', [], null, 'fas');
        $menuItem['class'] = $glyph->getClassNamesString();

        $menuItem[OptionsMenuRenderer::KEY_ID] = 0;
        $menuItem['sub'] = $subMenuItems;

        return array($menuItem);
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @return string
     */
    public function getUrlFormat(): string
    {
        return $this->urlFormat;
    }

    /**
     * @param string $urlFormat
     */
    public function setUrlFormat(string $urlFormat): void
    {
        $this->urlFormat = $urlFormat;
    }

    /**
     * @return string[]
     */
    public function renderAsList()
    {
        $renderer = new OptionsMenuRenderer();
        $this->render($renderer, 'sitemap');

        $rootName = $this->getTranslator()->trans('RootCategory', [], 'Chamilo\Core\Menu');

        return array('0' => $rootName) + $renderer->toArray();
    }

    /**
     * Renders the menu as a tree
     *
     * @return string The HTML formatted tree
     */
    public function renderAsTree()
    {
        $renderer = new TreeMenuRenderer('item-menu', '', '#', false);
        $this->render($renderer, 'sitemap');

        return $renderer->toHtml();
    }
}
