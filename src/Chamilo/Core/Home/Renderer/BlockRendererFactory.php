<?php
namespace Chamilo\Core\Home\Renderer;

use Chamilo\Core\Home\Storage\DataClass\Block;
use OutOfBoundsException;

/**
 * @package Chamilo\Core\Home\Renderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BlockRendererFactory
{
    /**
     * @var \Chamilo\Core\Home\Renderer\BlockRenderer[]
     */
    protected array $availableBlockRenderers;

    public function addAvailableBlockRenderer(BlockRenderer $blockRenderer): void
    {
        $this->availableBlockRenderers[get_class($blockRenderer)] = $blockRenderer;
    }

    public function getAvailableBlockRenderer(string $blockRendererType): BlockRenderer
    {
        if (!array_key_exists($blockRendererType, $this->availableBlockRenderers))
        {
            throw new OutOfBoundsException($blockRendererType . ' is not a valid BlockRenderer');
        }

        return $this->availableBlockRenderers[$blockRendererType];
    }

    /**
     * @return string[]
     */
    public function getAvailableBlockRendererTypes(): array
    {
        return array_keys($this->getAvailableBlockRenderers());
    }

    /**
     * @return \Chamilo\Core\Home\Renderer\BlockRenderer[]
     */
    public function getAvailableBlockRenderers(): array
    {
        return $this->availableBlockRenderers;
    }

    public function getRenderer(Block $block): BlockRenderer
    {
        $blockRendererType = $block->getContext() . '\Service\Home\\' . $block->getBlockType() . 'BlockRenderer';

        return $this->getAvailableBlockRenderer($blockRendererType);
    }
}
