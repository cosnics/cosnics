<?php
namespace Chamilo\Libraries\Calendar\Factory;

use Chamilo\Libraries\Calendar\Service\View\HtmlCalendarRenderer;
use Doctrine\Common\Collections\ArrayCollection;
use OutOfBoundsException;

/**
 * @package Chamilo\Libraries\Calendar\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HtmlCalendarRendererFactory extends ArrayCollection
{
    public function addHtmlCalendarRenderer(HtmlCalendarRenderer $htmlCalendarRenderer): void
    {
        $this->set($htmlCalendarRenderer->getType(), $htmlCalendarRenderer);
    }

    public function getHtmlCalendarRenderer(string $rendererType): HtmlCalendarRenderer
    {
        if (!$this->containsKey($rendererType)) {
            throw new OutOfBoundsException($rendererType . ' is not a valid HtmlCalendarRenderer');
        }

        return $this->get($rendererType);
    }
}
