<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait;

use Chamilo\Libraries\Architecture\Exception\ClassNotExistException;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonRendererRegistry;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonRendererCollectionTrait
{
    protected ButtonRendererRegistry $buttonRendererCollection;

    public function getButtonRendererCollection(): ButtonRendererRegistry
    {
        return $this->buttonRendererCollection;
    }

    public function setButtonRendererCollection(ButtonRendererRegistry $buttonRendererCollection): static
    {
        $this->buttonRendererCollection = $buttonRendererCollection;

        return $this;
    }

    public function renderSubButtons(ArrayCollection $buttons): string
    {
        $html = [];

        foreach ($buttons as $button) {
            try {
                $html[] = $this->getButtonRendererCollection()->getButtonRendererForButton($button)->render($button);
            }
            catch (ClassNotExistException) {
            }
        }

        return implode(PHP_EOL, $html);
    }
}