<?php
namespace Chamilo\Core\Menu\Renderer\Item;

use Chamilo\Core\Menu\Factory\ItemRendererFactory;
use Chamilo\Core\Menu\Renderer\ItemRenderer;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Service\RightsCacheService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Renderer\ItemRenderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CategoryItemRenderer extends ItemRenderer
{

    private ItemRendererFactory $itemRendererFactory;

    private RightsCacheService $rightsCacheService;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator,
        CachedItemService $itemCacheService, ChamiloRequest $request, RightsCacheService $rightsCacheService,
        ItemRendererFactory $itemRendererFactory
    )
    {
        parent::__construct($authorizationChecker, $translator, $itemCacheService, $request);

        $this->rightsCacheService = $rightsCacheService;
        $this->itemRendererFactory = $itemRendererFactory;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\CategoryItem $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function render(Item $item, User $user)
    {
        $html = [];

        $selected = $this->isSelected($item);

        $title = $this->renderTitle($item);

        $html[] = '<li class="' . implode(' ', $this->getClasses($selected)) . '">';
        $html[] =
            '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">';

        if ($item->showIcon())
        {
            $html[] = '<div>';

            if ($selected)
            {
                $glyph = new FontAwesomeGlyph(
                    'folder-open', ['fa-2x', 'fa-fw'], $title, 'fas'
                );
            }
            else
            {
                $glyph = new FontAwesomeGlyph(
                    'folder', ['fa-2x', 'fa-fw'], $title, 'fas'
                );
            }

            $html[] = $glyph->render();

            if (!$item->showTitle())
            {
                $html[] = '&nbsp;<span class="caret"></span>';
            }

            $html[] = '</div>';
        }

        if ($item->showTitle())
        {
            $html[] = '<div>' . $title . '&nbsp;<span class="caret"></span></div>';
        }

        $html[] = '</a>';

        if ($this->getItemCacheService()->doesItemHaveChildren($item))
        {
            $html[] = $this->renderChildren($item, $user);
        }

        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param bool $isSelected
     * @param string[] $existingClasses
     *
     * @return string[]
     */
    protected function getClasses($isSelected = false, $existingClasses = [])
    {
        $existingClasses[] = 'dropdown';

        return parent::getClasses($isSelected, $existingClasses);
    }

    /**
     * @return \Chamilo\Core\Menu\Factory\ItemRendererFactory
     */
    public function getItemRendererFactory(): ItemRendererFactory
    {
        return $this->itemRendererFactory;
    }

    /**
     * @return \Chamilo\Core\Menu\Service\RightsCacheService
     */
    public function getRightsCacheService(): RightsCacheService
    {
        return $this->rightsCacheService;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return bool
     */
    public function isSelected(Item $item)
    {
        $childItems = $this->getItemCacheService()->findItemsByParentIdentifier($item->getId());

        foreach ($childItems as $childItem)
        {
            $itemRenderer = $this->getItemRendererFactory()->getItemRenderer($childItem);

            if ($itemRenderer->isSelected($childItem))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    public function renderChildren(Item $item, User $user)
    {
        $childItems = $this->getItemCacheService()->findItemsByParentIdentifier($item->getId());

        $html = [];

        $html[] = '<ul class="dropdown-menu">';

        foreach ($childItems as $childItem)
        {
            $userCanViewItem = $this->getRightsCacheService()->canUserViewItem($user, $item);

            if ($userCanViewItem)
            {
                if (!$childItem->isHidden())
                {
                    $childItem->set_display(Item::DISPLAY_TEXT);

                    $itemRenderer = $this->getItemRendererFactory()->getItemRenderer($childItem);
                    $html[] = $itemRenderer->render($childItem, $user);
                }
            }
        }

        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Core\Menu\Factory\ItemRendererFactory $itemRendererFactory
     */
    public function setItemRendererFactory(ItemRendererFactory $itemRendererFactory): void
    {
        $this->itemRendererFactory = $itemRendererFactory;
    }

    /**
     * @param \Chamilo\Core\Menu\Service\RightsCacheService $rightsCacheService
     */
    public function setRightsService(RightsCacheService $rightsCacheService): void
    {
        $this->rightsCacheService = $rightsCacheService;
    }

}