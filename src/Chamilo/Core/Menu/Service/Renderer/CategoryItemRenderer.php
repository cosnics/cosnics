<?php
namespace Chamilo\Core\Menu\Service\Renderer;

use Chamilo\Core\Menu\Architecture\Interfaces\SelectableItemInterface;
use Chamilo\Core\Menu\Architecture\Interfaces\TranslatableItemInterface;
use Chamilo\Core\Menu\Architecture\Traits\TranslatableItemTrait;
use Chamilo\Core\Menu\Factory\ItemRendererFactory;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Service\RightsCacheService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Service\Renderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CategoryItemRenderer extends ItemRenderer implements TranslatableItemInterface
{
    use TranslatableItemTrait;

    private ItemRendererFactory $itemRendererFactory;

    private RightsCacheService $rightsCacheService;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator,
        CachedItemService $itemCacheService, ChamiloRequest $request, RightsCacheService $rightsCacheService,
        ItemRendererFactory $itemRendererFactory, array $fallbackIsoCodes
    )
    {
        parent::__construct($authorizationChecker, $translator, $itemCacheService, $request);

        $this->rightsCacheService = $rightsCacheService;
        $this->itemRendererFactory = $itemRendererFactory;
        $this->fallbackIsoCodes = $fallbackIsoCodes;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function render(Item $item, User $user): string
    {
        $html = [];

        $isSelected = $this->isSelected($item, $user);

        $title = $this->renderTitleForCurrentLanguage($item);

        $html[] = '<li class="dropdown' . ($isSelected ? ' active' : '') . '">';
        $html[] =
            '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">';

        if ($item->showIcon())
        {
            $html[] = '<div>';

            if ($isSelected)
            {
                $glyph = new FontAwesomeGlyph(
                    'folder-open', ['fa-2x', 'fa-fw'], $title, 'fas'
                );
            }
            else
            {
                $glyph = $this->getRendererTypeGlyph();
                $glyph->setExtraClasses(['fa-2x', 'fa-fw']);
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

    public function getItemRendererFactory(): ItemRendererFactory
    {
        return $this->itemRendererFactory;
    }

    public function getRendererTypeGlyph(): InlineGlyph
    {
        return new FontAwesomeGlyph('folder', ['fa-fw']);
    }

    public function getRendererTypeName(): string
    {
        return $this->getTranslator()->trans('CategoryItem', [], Manager::CONTEXT);
    }

    public function getRightsCacheService(): RightsCacheService
    {
        return $this->rightsCacheService;
    }

    public function isSelected(Item $item, User $user): bool
    {
        $childItems = $this->getItemCacheService()->findItemsByParentIdentifier($item->getId());

        foreach ($childItems as $childItem)
        {
            $itemRenderer = $this->getItemRendererFactory()->getItemRenderer($childItem);

            if ($itemRenderer instanceof SelectableItemInterface && $itemRenderer->isSelected($childItem, $user))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function renderChildren(Item $item, User $user): string
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
                    $childItem->setDisplay(Item::DISPLAY_TEXT);

                    $itemRenderer = $this->getItemRendererFactory()->getItemRenderer($childItem);
                    $html[] = $itemRenderer->render($childItem, $user);
                }
            }
        }

        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }

    public function renderTitleForCurrentLanguage(Item $item): string
    {
        return $this->determineItemTitleForCurrentLanguage($item);
    }

    public function renderTitleForIsoCode(Item $item, string $isoCode): string
    {
        return $this->determineItemTitleForIsoCode($item, $isoCode);
    }

}