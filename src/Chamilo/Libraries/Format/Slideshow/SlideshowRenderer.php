<?php
namespace Chamilo\Libraries\Format\Slideshow;

use Chamilo\Core\Repository\Common\Rendition\AbstractContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Format\Slideshow
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class SlideshowRenderer
{
    public const PARAM_AUTOPLAY = 'autoplay';
    public const PARAM_INDEX = 'slideshow';

    private ChamiloRequest $request;

    private Translator $translator;

    private UrlGenerator $urlGenerator;

    public function __construct(Translator $translator, UrlGenerator $urlGenerator, ChamiloRequest $request)
    {
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->request = $request;
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function render(
        $contentObjectRenditionContext, ContentObject $contentObject, int $contentObjectCount,
        array $contentObjectActions = [], array $actionParameters = []
    ): string
    {
        $slideshowIndex = $this->getSlideshowIndex();
        $translator = $this->getTranslator();

        if ($contentObjectCount == 0)
        {
            $html[] = Display::normal_message($translator->trans('SlideshowNoContentAvailable'));

            return implode(PHP_EOL, $html);
        }

        $html = [];

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12">';

        $html[] = '<div class="alert alert-info">' . $translator->trans('BrowserWarningPreview') . '</div>';

        $html[] = '<div class="panel panel-default panel-slideshow">';

        $html[] = '<div class="panel-heading">';
        $html[] =
            '<h3 class="panel-title">' . htmlspecialchars($contentObject->get_title()) . ' - ' . ($slideshowIndex + 1) .
            '/' . $contentObjectCount . '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';

        $html[] = '<div class="row">';
        $html[] = '<div class="col-lg-12 text-center">';
        $html[] =
            $this->getContentObjectRenditionImplementation($contentObject, $contentObjectRenditionContext)->render();
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="row panel-slideshow-actions">';
        $html[] = '<div class="col-xs-6 text-center">';
        $html[] = $this->renderPreviousNavigation($actionParameters);
        $html[] = '</div>';
        $html[] = '<div class="col-xs-6 text-center">';
        $html[] = $this->renderNextNavigation($contentObjectCount, $actionParameters);
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="row panel-slideshow-actions">';
        $html[] = '<div class="col-xs-12">';
        $html[] = $this->renderButtonToolbar($contentObjectActions, $actionParameters);
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->renderSlidshowAutoplay($contentObjectCount, $actionParameters);

        return implode(PHP_EOL, $html);
    }

    protected function determineUrl(array $parameters, int $slideshowIndex, int $slideshowAutoPlay): string
    {
        $parameters[self::PARAM_INDEX] = $slideshowIndex;
        $parameters[self::PARAM_AUTOPLAY] = $slideshowAutoPlay;

        return $this->getUrlGenerator()->fromParameters($parameters);
    }

    public function getContentObjectRenditionImplementation(ContentObject $contentObject, $contentObjectRenditionContext
    ): AbstractContentObjectRenditionImplementation
    {
        return ContentObjectRenditionImplementation::factory(
            $contentObject, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_PREVIEW,
            $contentObjectRenditionContext
        );
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function getSlideshowAutoPlay(): int
    {
        return $this->getRequest()->query->get(SlideshowRenderer::PARAM_AUTOPLAY, 0);
    }

    public function getSlideshowIndex(): int
    {
        return $this->getRequest()->query->get(SlideshowRenderer::PARAM_INDEX, 0);
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    protected function isFirst(): bool
    {
        if (!isset($this->isFirst))
        {
            $this->isFirst = ($this->getSlideshowIndex() == 0);
        }

        return $this->isFirst;
    }

    protected function isLast(int $contentObjectCount): bool
    {
        if (!isset($this->isLast))
        {
            $this->isLast = ($this->getSlideshowIndex() == ($contentObjectCount - 1));
        }

        return $this->isLast;
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    protected function renderButtonToolbar(array $contentObjectActions, array $parameters): string
    {
        $slideshowIndex = $this->getSlideshowIndex();
        $slideshowAutoplay = $this->getSlideshowAutoPlay();
        $translator = $this->getTranslator();

        $actionsToolBar = new Toolbar();
        $actionsToolBar->add_items($contentObjectActions);

        if ($slideshowAutoplay)
        {
            $actionsToolBar->add_item(
                new ToolbarItem(
                    $translator->trans('Stop', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('stop'),
                    $this->determineUrl($parameters, $slideshowIndex, 0), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $actionsToolBar->add_item(
                new ToolbarItem(
                    $translator->trans('Play', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('play'),
                    $this->determineUrl($parameters, $slideshowIndex, 1), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        $actionsToolBarRenderer = new ButtonToolBarRenderer($actionsToolBar->convertToButtonToolBar(false));

        return $actionsToolBarRenderer->render();
    }

    /**
     * @throws \Exception
     */
    protected function renderNextNavigation(int $contentobjectCount, array $parameters): string
    {
        $html = [];

        if (!$this->isLast($contentobjectCount))
        {
            $slideshowIndex = $this->getSlideshowIndex() + 1;

            $glyph = new FontAwesomeGlyph('angle-right', ['fa-4x', 'fa-fw'], null, 'fas');
            $html[] =
                '<a href="' . $this->determineUrl($parameters, $slideshowIndex, $this->getSlideshowAutoPlay()) . '">' .
                $glyph->render() . '</a>';

            $slideshowIndex = $contentobjectCount - 1;

            $glyph = new FontAwesomeGlyph('angle-double-right', ['fa-4x', 'fa-fw'], null, 'fas');
            $html[] =
                '<a href="' . $this->determineUrl($parameters, $slideshowIndex, $this->getSlideshowAutoPlay()) . '">' .
                $glyph->render() . '</span></a>';
        }
        else
        {
            $glyph = new FontAwesomeGlyph('angle-right', ['fa-4x', 'fa-fw', 'text-muted'], null, 'fas');
            $html[] = $glyph->render();

            $glyph = new FontAwesomeGlyph('angle-double-right', ['fa-4x', 'fa-fw', 'text-muted'], null, 'fas');
            $html[] = $glyph->render();
        }

        return implode('', $html);
    }

    /**
     * @throws \Exception
     */
    protected function renderPreviousNavigation(array $parameters): string
    {
        $html = [];

        if (!$this->isFirst())
        {
            $glyph = new FontAwesomeGlyph('angle-double-left', ['fa-4x', 'fa-fw'], null, 'fas');
            $html[] = '<a href="' . $this->determineUrl($parameters, 0, $this->getSlideshowAutoPlay()) . '">' .
                $glyph->render() . '</a>';

            $slideshowIndex = $this->getSlideshowIndex() - 1;

            $glyph = new FontAwesomeGlyph('angle-left', ['fa-4x', 'fa-fw'], null, 'fas');
            $html[] =
                '<a href="' . $this->determineUrl($parameters, $slideshowIndex, $this->getSlideshowAutoPlay()) . '">' .
                $glyph->render() . '</span></a>';
        }
        else
        {
            $glyph = new FontAwesomeGlyph('angle-double-left', ['fa-4x', 'fa-fw', 'text-muted'], null, 'fas');
            $html[] = $glyph->render();

            $glyph = new FontAwesomeGlyph('angle-left', ['fa-4x', 'fa-fw', 'text-muted'], null, 'fas');
            $html[] = $glyph->render();
        }

        return implode('', $html);
    }

    /**
     * @throws \Exception
     */
    protected function renderSlidshowAutoplay(int $contentobjectCount, array $parameters): string
    {
        $slideshowAutoplay = $this->getSlideshowAutoPlay();

        $html = [];

        if ($slideshowAutoplay)
        {
            if (!$this->isLast($contentobjectCount))
            {
                $slideshowIndex = $this->getSlideshowIndex() + 1;
                $autoplayUrl = $this->determineUrl($parameters, $slideshowIndex, 1);
            }
            else
            {
                $autoplayUrl = $this->determineUrl($parameters, 0, 1);
            }

            $html[] = '<meta http-equiv="Refresh" content="10; url=' . $autoplayUrl . '" />';
        }

        return implode(PHP_EOL, $html);
    }
}