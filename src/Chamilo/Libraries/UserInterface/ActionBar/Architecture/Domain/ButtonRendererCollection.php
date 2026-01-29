<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain;

use Chamilo\Libraries\Architecture\Exception\ClassNotExistException;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Service\ButtonRenderer;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ButtonRendererCollection extends ArrayCollection
{

    public function addButtonRenderer(ButtonRenderer $buttonRenderer): void
    {
        $this->set(get_class($buttonRenderer), $buttonRenderer);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function getButtonRenderer(string $buttonRendererType): ButtonRenderer
    {
        if (!$this->containsKey($buttonRendererType))
        {
            throw new ClassNotExistException($buttonRendererType);
        }

        return $this->get($buttonRendererType);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function getButtonRendererForButton(ButtonInterface $button): ButtonRenderer
    {
        return $this->getButtonRenderer($button->getButtonRendererClass());
    }

    /**
     * @return string[]
     */
    public function getButtonRendererTypes(): array
    {
        return $this->getKeys();
    }

    /**
     * @return \Chamilo\Core\Home\UserInterface\HomeRenderer\BlockRenderer[]
     */
    public function getButtonRenderers(): array
    {
        return $this->toArray();
    }

}
