<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ButtonRendererRegistry
{
    public function __construct(protected ArrayCollection $buttonRenderers = new ArrayCollection())
    {
    }

    public function addButtonRenderer(ButtonRendererInterface $buttonRenderer): void
    {
        $this->buttonRenderers->set(get_class($buttonRenderer), $buttonRenderer);
    }

    /**
     * @template tGetButtonRenderer
     * @param class-string<tGetButtonRenderer> $buttonRendererClassName
     *
     * @return tGetButtonRenderer|ButtonRendererInterface
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function getButtonRenderer(string $buttonRendererClassName): ButtonRendererInterface
    {
        if (!$this->hasButtonRenderer($buttonRendererClassName)) {
            throw new NoSuchClassException($buttonRendererClassName, ButtonRendererInterface::class);
        }

        return $this->buttonRenderers->get($buttonRendererClassName);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function getButtonRendererForButton(ButtonInterface $button): ButtonRendererInterface
    {
        return $this->getButtonRenderer($button->getButtonRendererClassName());
    }

    public function hasButtonRenderer(string $buttonRendererClass): bool
    {
        return $this->buttonRenderers->containsKey($buttonRendererClass);
    }
}