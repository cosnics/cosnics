<?php
namespace Chamilo\Core\Menu\Renderer\NavigationBarRenderer;

use Chamilo\Core\Menu\Renderer\ItemRendererFactory;
use Chamilo\Core\Menu\Renderer\NavigationBarRenderer;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Service\RightsService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Theme;

/**
 * @package Chamilo\Core\Menu\Renderer\NavigationBarRenderer
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CategoryItemRenderer extends NavigationBarItemRenderer
{
    /**
     * @var \Chamilo\Core\Menu\Service\ItemService
     */
    private $itemService;

    /**
     * @var \Chamilo\Core\Menu\Service\RightsService
     */
    private $rightsService;

    /**
     * @var \Chamilo\Core\Menu\Renderer\ItemRendererFactory
     */
    private $itemRendererFactory;

    /**
     * @var \Chamilo\Libraries\Format\Theme
     */
    private $themeUtilities;

    /**
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     * @param \Chamilo\Core\Menu\Service\RightsService $rightsService
     * @param \Chamilo\Core\Menu\Renderer\ItemRendererFactory $itemRendererFactory
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     */
    public function __construct(
        ItemService $itemService, RightsService $rightsService, ItemRendererFactory $itemRendererFactory,
        Theme $themeUtilities
    )
    {
        $this->itemService = $itemService;
        $this->rightsService = $rightsService;
        $this->itemRendererFactory = $itemRendererFactory;
        $this->themeUtilities = $themeUtilities;
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
     * @return \Chamilo\Core\Menu\Service\RightsService
     */
    public function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    /**
     * @param \Chamilo\Core\Menu\Service\RightsService $rightsService
     */
    public function setRightsService(RightsService $rightsService): void
    {
        $this->rightsService = $rightsService;
    }

    /**
     * @return \Chamilo\Core\Menu\Renderer\ItemRendererFactory
     */
    public function getItemRendererFactory(): ItemRendererFactory
    {
        return $this->itemRendererFactory;
    }

    /**
     * @param \Chamilo\Core\Menu\Renderer\ItemRendererFactory $itemRendererFactory
     */
    public function setItemRendererFactory(ItemRendererFactory $itemRendererFactory): void
    {
        $this->itemRendererFactory = $itemRendererFactory;
    }

    /**
     * @return \Chamilo\Libraries\Format\Theme
     */
    public function getThemeUtilities(): Theme
    {
        return $this->themeUtilities;
    }

    /**
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     */
    public function setThemeUtilities(Theme $themeUtilities): void
    {
        $this->themeUtilities = $themeUtilities;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\CategoryItem $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    public function render(Item $item, User $user)
    {
        $html = array();

        $selected = $this->isSelected($item);

        $title = $this->getItemService()->getItemTitleForCurrentLanguage($item);

        $html[] = '<li class="dropdown' . ($selected ? ' active' : '') . '">';
        $html[] =
            '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">';

        if ($item->showIcon())
        {
            if (!empty($item->getIconClass()))
            {
                $html[] = $this->renderCssIcon($item);
            }
            else
            {
                $imagePath = $this->getThemeUtilities()->getImagePath(
                    'Chamilo\Core\Menu', 'Menu/Folder' . ($selected ? 'Selected' : '')
                );

                $html[] = '<img class="chamilo-menu-item-icon' .
                    ($item->showTitle() ? ' chamilo-menu-item-image-with-label' : '') . '" src="' . $imagePath .
                    '" title="' . htmlentities($title) . '" alt="' . $title . '" />';
            }
        }

        if ($item->showTitle())
        {
            $html[] = '<div class="chamilo-menu-item-label' .
                ($item->showIcon() ? ' chamilo-menu-item-label-with-image' : '') . '">' . $title . '</div>';
        }

        $html[] = '<div class="clearfix"></div>';
        $html[] = '</a>';

        if ($this->getItemService()->doesItemHaveChildren($item))
        {
            $html[] = $this->renderChildren($item, $user);
        }

        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    public function renderChildren(Item $item, User $user)
    {
        $childItems = $this->getItemService()->findItemsByParentIdentifier($item->getId());

        $html = array();

        $html[] = '<ul class="dropdown-menu">';

        foreach ($childItems as $childItem)
        {
            $userCanViewItem = $this->getRightsService()->canUserViewItem($user, $item);

            if ($userCanViewItem)
            {
                if (!$childItem->isHidden())
                {
                    $itemRenderer =
                        $this->getItemRendererFactory()->getItemRenderer(NavigationBarRenderer::class, $childItem);
                    $html[] = $itemRenderer->render($childItem, $user);
                }
            }
        }

        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return boolean
     */
    public function isSelected(Item $item)
    {
        $childItems = $this->getItemService()->findItemsByParentIdentifier($item->getId());

        foreach ($childItems as $childItem)
        {
            $itemRenderer = $this->getItemRendererFactory()->getItemRenderer(NavigationBarRenderer::class, $childItem);

            if ($itemRenderer->isSelected($childItem))
            {
                return true;
            }
        }

        return false;
    }

}