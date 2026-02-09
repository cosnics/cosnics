<?php
namespace Chamilo\Core\Menu\Architecture\Domain;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\UserInterface\MenuRenderer\ItemRenderer;
use Doctrine\Common\Collections\ArrayCollection;
use OutOfBoundsException;

/**
 * @package Chamilo\Core\Menu\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemRendererRegistry extends ArrayCollection
{
    public function addItemRenderer(ItemRenderer $itemRenderer): void
    {
        $this->set(get_class($itemRenderer), $itemRenderer);
    }

    public function getItemRenderer(string $itemRendererType): ItemRenderer
    {
        if (!$this->containsKey($itemRendererType)) {
            throw new OutOfBoundsException($itemRendererType . ' is not a valid ItemRenderer');
        }

        return $this->get($itemRendererType);
    }

    public function getItemRendererForItem(Item $item): ItemRenderer
    {
        return $this->getItemRenderer($item->getType());
    }

    /**
     * @return string[]
     */
    public function getItemRendererTypes(): array
    {
        return $this->getKeys();
    }

    /**
     * @return \Chamilo\Core\Menu\UserInterface\MenuRenderer\ItemRenderer[]
     */
    public function getItemRenderers(): array
    {
        return $this->toArray();
    }
}