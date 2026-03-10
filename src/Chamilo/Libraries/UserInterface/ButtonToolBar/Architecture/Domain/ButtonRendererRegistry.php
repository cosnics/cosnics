<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain;

use Chamilo\Libraries\Protocol\Error\Architecture\Exception\NoSuchClassException;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ButtonRendererRegistry extends ArrayCollection
{
    public function addButtonRenderer(ButtonRendererInterface $buttonRenderer): void
    {
        $this->set(get_class($buttonRenderer), $buttonRenderer);
    }

    /**
     * @template tGetButtonRenderer
     * @param class-string<tGetButtonRenderer> $buttonRendererClassName
     *
     * @return tGetButtonRenderer|ButtonRendererInterface
     * @throws \Chamilo\Libraries\Protocol\Error\Architecture\Exception\NoSuchClassException
     */
    public function getButtonRenderer(string $buttonRendererClassName): ButtonRendererInterface
    {
        if (!$this->hasButtonRenderer($buttonRendererClassName)) {
            throw new NoSuchClassException($buttonRendererClassName, ButtonRendererInterface::class);
        }

        return $this->get($buttonRendererClassName);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Error\Architecture\Exception\NoSuchClassException
     */
    public function getButtonRendererForButton(ButtonInterface $button): ButtonRendererInterface
    {
        return $this->getButtonRenderer($button->getButtonRendererClassName());
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