<?php
namespace Chamilo\Libraries\Calendar\Factory;

use Chamilo\Libraries\Calendar\Service\View\HtmlCalendarRenderer;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HtmlCalendarRendererFactory extends ArrayCollection
{
    protected Translator $translator;

    public function __construct(Translator $translator)
    {
        parent::__construct();

        $this->translator = $translator;
    }

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

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}
