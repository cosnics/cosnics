<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait;

use Chamilo\Libraries\Architecture\Exception\ClassNotExistException;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonRendererCollection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonRendererCollectionTrait
{
    protected ButtonRendererCollection $buttonRendererCollection;

    public function getButtonRendererCollection(): ButtonRendererCollection
    {
        return $this->buttonRendererCollection;
    }

    public function setButtonRendererCollection(ButtonRendererCollection $buttonRendererCollection): static
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