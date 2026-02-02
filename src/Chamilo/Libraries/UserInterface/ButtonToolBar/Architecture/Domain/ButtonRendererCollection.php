<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain;

use Chamilo\Libraries\Architecture\Exception\ClassNotExistException;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ButtonRendererCollection extends ArrayCollection
{
    public function addButtonRenderer(ButtonRendererInterface $buttonRenderer): void
    {
        $this->set(get_class($buttonRenderer), $buttonRenderer);
    }

    /**
     * @template tGetButtonRenderer
     * @param class-string<tGetButtonRenderer> $buttonRendererClass
     *
     * @return tGetButtonRenderer|ButtonRendererInterface
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function getButtonRenderer(string $buttonRendererClass): ButtonRendererInterface
    {
        if (!$this->hasButtonRenderer($buttonRendererClass)) {
            throw new ClassNotExistException($buttonRendererClass);
        }

        return $this->get($buttonRendererClass);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function getButtonRendererForButton(ButtonInterface $button): ButtonRendererInterface
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
     * @return \Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererInterface[]
     */
    public function getButtonRenderers(): array
    {
        return $this->toArray();
    }

    public function hasButtonRenderer(string $buttonRendererClass): bool
    {
        return $this->containsKey($buttonRendererClass);
    }
}