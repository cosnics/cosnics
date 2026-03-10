<?php
namespace Chamilo\Core\Menu\Architecture\Domain;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\UserInterface\MenuRenderer\ItemRenderer;
use Chamilo\Libraries\Protocol\Error\Architecture\Exception\NoSuchClassException;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemRendererRegistry extends ArrayCollection
{
    protected Translator $translator;

    public function __construct(Translator $translator)
    {
        parent::__construct();

        $this->translator = $translator;
    }

    public function addItemRenderer(ItemRenderer $itemRenderer): void
    {
        $this->set(get_class($itemRenderer), $itemRenderer);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Error\Architecture\Exception\NoSuchClassException
     */
    public function getItemRenderer(string $itemRendererType): ItemRenderer
    {
        if (!$this->containsKey($itemRendererType)) {
            throw new NoSuchClassException($itemRendererType, ItemRenderer::class);
        }

        return $this->get($itemRendererType);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Error\Architecture\Exception\NoSuchClassException
     */
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

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}