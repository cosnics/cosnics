<?php
namespace Chamilo\Libraries\Format\Menu\TreeMenu;

use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuArrayRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;

/**
 *
 * @package Chamilo\Libraries\Format\Menu\TreeMenu
 */
class TreeMenu extends HtmlMenu
{

    protected HtmlMenuArrayRenderer $htmlMenuArrayRenderer;

    private TreeMenuDataProvider $dataProvider;

    private string $name;

    public function __construct($name, TreeMenuDataProvider $data_provider)
    {
        $this->name = $name;
        $this->dataProvider = $data_provider;

        parent::__construct($this->getMenuItems());

        $this->htmlMenuArrayRenderer = new HtmlMenuArrayRenderer();
        $this->forceCurrentUrl($this->dataProvider->getSelectedTreeMenuItemUrl());
    }

    public function getBreadcrumbs(): BreadcrumbTrail
    {
        $this->render($this->htmlMenuArrayRenderer, 'urhere');
        $breadcrumbs = $this->htmlMenuArrayRenderer->toArray();
        $trail = $this->getBreadcrumbTrail();
        $i = 0;

        foreach ($breadcrumbs as $crumb)
        {
            if ($i == 0)
            {
                $i ++;
                continue;
            }

            $trail->add(new Breadcrumb($crumb['url'], substr($crumb['title'], 0, strpos($crumb['title'], '(') - 1)));
        }

        return $trail;
    }

    /**
     *
     * @return string[]
     */
    public function getMenuItems(): array
    {
        $menu_items = [];
        $menu_items[] = $this->dataProvider->getTreeMenuData()->toArray();

        return $menu_items;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function renderAsTree(): string
    {
        $renderer = new TreeMenuRenderer($this->getName());
        $this->render($renderer, 'sitemap');

        return $renderer->toHtml();
    }
}
