<?php
namespace Chamilo\Core\Home\Architecture\Domain;

use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\UserInterface\HomeRenderer\BlockRenderer;
use Doctrine\Common\Collections\ArrayCollection;
use OutOfBoundsException;

/**
 * @package Chamilo\Core\Home\Renderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BlockRendererCollection extends ArrayCollection
{

    public function addBlockRenderer(BlockRenderer $blockRenderer): void
    {
        $this->set(get_class($blockRenderer), $blockRenderer);
    }

    public function getBlockRenderer(string $blockRendererType): BlockRenderer
    {
        if (!$this->containsKey($blockRendererType))
        {
            throw new OutOfBoundsException($blockRendererType . ' is not a valid BlockRenderer');
        }

        return $this->get($blockRendererType);
    }

    /**
     * @return string[]
     */
    public function getBlockRendererTypes(): array
    {
        return $this->getKeys();
    }

    /**
     * @return \Chamilo\Core\Home\UserInterface\HomeRenderer\BlockRenderer[]
     */
    public function getBlockRenderers(): array
    {
        return $this->toArray();
    }

    public function getRendererForElement(Element $block): BlockRenderer
    {
        return $this->getBlockRenderer($block->getBlockType());
    }

}
