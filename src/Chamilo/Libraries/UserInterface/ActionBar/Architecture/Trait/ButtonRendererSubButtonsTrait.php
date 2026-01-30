<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait;

use Chamilo\Libraries\Architecture\Exception\ClassNotExistException;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonRendererSubButtonsTrait
{
    use ButtonRendererCollectionTrait;

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