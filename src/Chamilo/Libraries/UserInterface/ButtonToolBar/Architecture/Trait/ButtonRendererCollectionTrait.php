<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonRendererRegistry;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonRendererCollectionTrait
{
    protected ButtonRendererRegistry $buttonRendererRegistry;

    public function renderSubButtons(ArrayCollection $buttons): string
    {
        $html = [];

        foreach ($buttons as $button) {
            try {
                $html[] = $this->buttonRendererRegistry->getButtonRendererForButton($button)->render($button);
            }
            catch (NoSuchClassException) {
            }
        }

        return implode(PHP_EOL, $html);
    }
}