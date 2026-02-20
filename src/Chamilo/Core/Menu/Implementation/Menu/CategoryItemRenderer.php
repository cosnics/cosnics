<?php
namespace Chamilo\Core\Menu\Implementation\Menu;

use Chamilo\Core\Menu\Architecture\Domain\ItemRendererRegistry;
use Chamilo\Core\Menu\Architecture\Interface\SelectableItemInterface;
use Chamilo\Core\Menu\Architecture\Interface\TranslatableItemInterface;
use Chamilo\Core\Menu\Architecture\Trait\TranslatableItemTrait;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\UserInterface\MenuRenderer\ItemRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Implementation\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CategoryItemRenderer extends ItemRenderer implements TranslatableItemInterface
{
    use TranslatableItemTrait;

    private ItemRendererRegistry $itemRendererFactory;

    public function __construct(
        Translator $translator, CachedItemService $itemCacheService, ChamiloRequest $request,
        ItemRendererRegistry $itemRendererFactory, array $fallbackIsoCodes
    )
    {
        parent::__construct($translator, $itemCacheService, $request);

        $this->itemRendererFactory = $itemRendererFactory;
        $this->fallbackIsoCodes = $fallbackIsoCodes;
    }

    public function render(Item $item, User $user): string
    {
        $html = [];

        $isSelected = $this->isSelected($item, $user);

        $title = $this->renderTitleForCurrentLanguage($item);

        $html[] = '<li class="nav-item dropdown">';
        $html[] = '<a href="#" class="nav-link dropdown-toggle' . ($isSelected ? ' active' : '') .
            '" data-bs-toggle="dropdown" aria-expanded="false">';

        if ($item->showIcon()) {
            if ($isSelected) {
                $glyph = new FontAwesomeGlyph(
                    'folder-open', ['fa-2x', 'fa-fw'], $title, 'fas'
                );
            }
            else {
                $glyph = $this->getRendererTypeGlyph();
                $glyph->setExtraClasses(['fa-2x', 'fa-fw']);
            }

            $html[] = $glyph->render();
        }

        if ($item->showTitle()) {
            $html[] = '<span>' . $title . '</span>';
        }

        $html[] = '</a>';

        if ($this->getItemCacheService()->doesItemHaveChildren($item)) {
            $html[] = $this->renderChildren($item, $user);
        }

        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    public function getItemRendererFactory(): ItemRendererRegistry
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

    public function isSelected(Item $item, User $user): bool
    {
        $childItems = $this->getItemCacheService()->findItemsByParentIdentifier($item->getId());

        foreach ($childItems as $childItem) {
            $itemRenderer = $this->getItemRendererFactory()->getItemRendererForItem($childItem);

            if ($itemRenderer instanceof SelectableItemInterface && $itemRenderer->isSelected($childItem, $user)) {
                return true;
            }
        }

        return false;
    }

    public function renderChildren(Item $item, User $user): string
    {
        $childItems = $this->getItemCacheService()->findItemsByParentIdentifier($item->getId());

        $html = [];

        $html[] = '<ul class="dropdown-menu dropdown-menu-end">';

        foreach ($childItems as $childItem) {
            if (!$childItem->isHidden()) {
                $childItem->setDisplay(DisplayTypeEnum::LABEL);

                $itemRenderer = $this->getItemRendererFactory()->getItemRendererForItem($childItem);
                $html[] = $itemRenderer->render($childItem, $user);
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