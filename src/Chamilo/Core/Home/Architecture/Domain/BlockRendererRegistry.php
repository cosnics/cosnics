<?php
namespace Chamilo\Core\Home\Architecture\Domain;

use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\UserInterface\HomeRenderer\BlockRenderer;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Home\Architecture\Domain
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BlockRendererRegistry extends ArrayCollection
{
    public function addBlockRenderer(BlockRenderer $blockRenderer): void
    {
        $this->set(get_class($blockRenderer), $blockRenderer);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function getBlockRenderer(string $blockRendererType): BlockRenderer
    {
        if (!$this->containsKey($blockRendererType)) {
            throw new NoSuchClassException($blockRendererType, BlockRenderer::class);
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

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function getRendererForElement(Element $block): BlockRenderer
    {
        return $this->getBlockRenderer($block->getBlockType());
    }
}
