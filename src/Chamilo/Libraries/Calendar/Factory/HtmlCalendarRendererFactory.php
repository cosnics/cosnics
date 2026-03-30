<?php
namespace Chamilo\Libraries\Calendar\Factory;

use Chamilo\Libraries\Calendar\Service\View\HtmlCalendarRenderer;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException;
use Doctrine\Common\Collections\ArrayCollection;

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

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function getHtmlCalendarRenderer(string $rendererType): HtmlCalendarRenderer
    {
        if (!$this->containsKey($rendererType)) {
            throw new NoSuchClassException($rendererType, HtmlCalendarRenderer::class);
        }

        return $this->get($rendererType);
    }
}
