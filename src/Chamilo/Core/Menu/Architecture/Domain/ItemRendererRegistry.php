<?php
namespace Chamilo\Core\Menu\Architecture\Domain;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\UserInterface\MenuRenderer\ItemRenderer;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Menu\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemRendererRegistry
{
    public function __construct(protected ArrayCollection $itemRenderers = new ArrayCollection())
    {
    }

    public function addItemRenderer(ItemRenderer $itemRenderer): void
    {
        $this->itemRenderers->set(get_class($itemRenderer), $itemRenderer);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function getItemRenderer(string $itemRendererType): ItemRenderer
    {
        if (!$this->itemRenderers->containsKey($itemRendererType)) {
            throw new NoSuchClassException($itemRendererType, ItemRenderer::class);
        }

        return $this->itemRenderers->get($itemRendererType);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function getItemRendererForItem(Item $item): ItemRenderer
    {
        return $this->getItemRenderer($item->getType());
    }

    /**
     * @return \Chamilo\Core\Menu\UserInterface\MenuRenderer\ItemRenderer[]
     */
    public function getItemRenderers(): array
    {
        return $this->itemRenderers->toArray();
    }
}