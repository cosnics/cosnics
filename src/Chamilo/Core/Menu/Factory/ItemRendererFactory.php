<?php
namespace Chamilo\Core\Menu\Factory;

use Chamilo\Core\Menu\Renderer\ItemRenderer;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use OutOfBoundsException;

/**
 * @package Chamilo\Core\Menu\Factory
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemRendererFactory
{

    /**
     * @var \Chamilo\Core\Menu\Renderer\ItemRenderer[]
     */
    protected array $availableItemRenderers;

    public function addAvailableItemRenderer(ItemRenderer $itemRenderer): void
    {
        $this->availableItemRenderers[get_class($itemRenderer)] = $itemRenderer;
    }

    public function getAvailableItemRenderer(string $itemRendererType): ItemRenderer
    {
        if (!array_key_exists($itemRendererType, $this->availableItemRenderers))
        {
            throw new OutOfBoundsException($itemRendererType . ' is not a valid ItemRenderer');
        }

        return $this->availableItemRenderers[$itemRendererType];
    }

    /**
     * @return string[]
     */
    public function getAvailableItemRendererTypes(): array
    {
        return array_keys($this->getAvailableItemRenderers());
    }

    /**
     * @return \Chamilo\Core\Menu\Renderer\ItemRenderer[]
     */
    public function getAvailableItemRenderers(): array
    {
        return $this->availableItemRenderers;
    }

    public function getItemRenderer(Item $item): ItemRenderer
    {
        return $this->getAvailableItemRenderer($item->getItemType());
    }
}