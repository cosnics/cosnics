<?php
namespace Chamilo\Libraries\Format\Slideshow;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Format\Slideshow
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class SlideshowRenderer
{
    public const PARAM_AUTOPLAY = 'autoplay';
    public const PARAM_INDEX = 'slideshow';

    /**
     * @var string[]
     */
    private array $actionParameters;

    private ContentObject $contentObject;

    private int $contentObjectCount;

    private ContentObjectRenditionImplementation $contentObjectRenditionImplementation;

    private bool $isFirst;

    private bool $isLast;

    /**
     * @var \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    private array $slideActions;

    private int $slideshowAutoPlay;

    private int $slideshowIndex;

    /**
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param int $contentObjectCount
     * @param \Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation $contentObjectRenditionImplementation
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem[] $slideActions
     * @param string[] $actionParameters
     * @param int $slideshowIndex
     * @param int $slideshowAutoplay
     */
    public function __construct(
        ContentObject $contentObject, int $contentObjectCount,
        ContentObjectRenditionImplementation $contentObjectRenditionImplementation, array $slideActions,
        array $actionParameters, int $slideshowIndex, int $slideshowAutoplay
    )
    {
        $this->contentObject = $contentObject;
        $this->contentObjectCount = $contentObjectCount;
        $this->contentObjectRenditionImplementation = $contentObjectRenditionImplementation;
        $this->slideActions = $slideActions;
        $this->actionParameters = $actionParameters;
        $this->slideshowIndex = $slideshowIndex;
        $this->slideshowAutoPlay = $slideshowAutoplay;
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function render(): string
    {
        $slideshowIndex = $this->getSlideshowIndex();
        $contentObject = $this->getContentObject();
        $contentObjectCount = $this->getContentObjectCount();

        if ($contentObjectCount == 0)
        {
            $html[] = Display::normal_message(Translation::get('SlideshowNoContentAvailable'));

            return implode(PHP_EOL, $html);
        }

        $html = [];

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12">';

        $html[] = '<div class="alert alert-info">' . Translation::get('BrowserWarningPreview') . '</div>';

        $html[] = '<div class="panel panel-default panel-slideshow">';

        $html[] = '<div class="panel-heading">';
        $html[] =
            '<h3 class="panel-title">' . htmlspecialchars($contentObject->get_title()) . ' - ' . ($slideshowIndex + 1) .
            '/' . $contentObjectCount . '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';

        $html[] = '<div class="row">';
        $html[] = '<div class="col-lg-12 text-center">';
        $html[] = $this->getContentObjectRenditionImplementation()->render();
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="row panel-slideshow-actions">';
        $html[] = '<div class="col-xs-6 text-center">';
        $html[] = $this->renderPreviousNavigation();
        $html[] = '</div>';
        $html[] = '<div class="col-xs-6 text-center">';
        $html[] = $this->renderNextNavigation();
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="row panel-slideshow-actions">';
        $html[] = '<div class="col-xs-12">';
        $html[] = $this->renderButtonToolbar();
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->renderSlidshowAutoplay();

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Exception
     */
    public function determineUrl(int $slideshowIndex, int $slideshowAutoPlay): string
    {
        $parameters = $this->getActionParameters();

        $parameters[self::PARAM_INDEX] = $slideshowIndex;
        $parameters[self::PARAM_AUTOPLAY] = $slideshowAutoPlay;

        $redirect = new Redirect($parameters);

        return $redirect->getUrl();
    }

    /**
     * @return string[]
     */
    public function getActionParameters(): array
    {
        return $this->actionParameters;
    }

    /**
     * @param string[] $actionParameters
     */
    public function setActionParameters(array $actionParameters)
    {
        $this->actionParameters = $actionParameters;
    }

    public function getContentObject(): ContentObject
    {
        return $this->contentObject;
    }

    public function setContentObject(ContentObject $contentObject)
    {
        $this->contentObject = $contentObject;
    }

    public function getContentObjectCount(): int
    {
        return $this->contentObjectCount;
    }

    public function setContentObjectCount(int $contentObjectCount)
    {
        $this->contentObjectCount = $contentObjectCount;
    }

    public function getContentObjectRenditionImplementation(): ContentObjectRenditionImplementation
    {
        return $this->contentObjectRenditionImplementation;
    }

    public function setContentObjectRenditionImplementation(
        ContentObjectRenditionImplementation $contentObjectRenditionImplementation
    )
    {
        $this->contentObjectRenditionImplementation = $contentObjectRenditionImplementation;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    public function getSlideActions(): array
    {
        return $this->slideActions;
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem[] $slideActions
     */
    public function setSlideActions(array $slideActions)
    {
        $this->slideActions = $slideActions;
    }

    public function getSlideshowAutoPlay(): int
    {
        return $this->slideshowAutoPlay;
    }

    public function setSlideshowAutoPlay(int $slideshowAutoPlay)
    {
        $this->slideshowAutoPlay = $slideshowAutoPlay;
    }

    public function getSlideshowIndex(): int
    {
        return $this->slideshowIndex;
    }

    public function setSlideshowIndex(int $slideshowIndex)
    {
        $this->slideshowIndex = $slideshowIndex;
    }

    public function isFirst(): bool
    {
        if (!isset($this->isFirst))
        {
            $this->isFirst = ($this->getSlideshowIndex() == 0);
        }

        return $this->isFirst;
    }

    public function isLast(): bool
    {
        if (!isset($this->isLast))
        {
            $this->isLast = ($this->getSlideshowIndex() == ($this->getContentObjectCount() - 1));
        }

        return $this->isLast;
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function renderButtonToolbar(): string
    {
        $slideshowIndex = $this->getSlideshowIndex();
        $slideshowAutoplay = $this->getSlideshowAutoPlay();

        $actionsToolBar = new Toolbar();
        $actionsToolBar->add_items($this->getSlideActions());

        if ($slideshowAutoplay)
        {
            $actionsToolBar->add_item(
                new ToolbarItem(
                    Translation::get('Stop', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('stop'),
                    $this->determineUrl($slideshowIndex, 0), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $actionsToolBar->add_item(
                new ToolbarItem(
                    Translation::get('Play', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('play'),
                    $this->determineUrl($slideshowIndex, 1), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        $actionsToolBarRenderer = new ButtonToolBarRenderer($actionsToolBar->convertToButtonToolBar(false));

        return $actionsToolBarRenderer->render();
    }

    /**
     * @throws \Exception
     */
    public function renderNextNavigation(): string
    {
        $html = [];

        if (!$this->isLast())
        {
            $slideshowIndex = $this->getSlideshowIndex() + 1;

            $glyph = new FontAwesomeGlyph('angle-right', ['fa-4x', 'fa-fw'], null, 'fas');
            $html[] = '<a href="' . $this->determineUrl($slideshowIndex, $this->getSlideshowAutoPlay()) . '">' .
                $glyph->render() . '</a>';

            $slideshowIndex = $this->getContentObjectCount() - 1;

            $glyph = new FontAwesomeGlyph('angle-double-right', ['fa-4x', 'fa-fw'], null, 'fas');
            $html[] = '<a href="' . $this->determineUrl($slideshowIndex, $this->getSlideshowAutoPlay()) . '">' .
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
    public function renderPreviousNavigation(): string
    {
        $html = [];

        if (!$this->isFirst())
        {
            $glyph = new FontAwesomeGlyph('angle-double-left', ['fa-4x', 'fa-fw'], null, 'fas');
            $html[] =
                '<a href="' . $this->determineUrl(0, $this->getSlideshowAutoPlay()) . '">' . $glyph->render() . '</a>';

            $slideshowIndex = $this->getSlideshowIndex() - 1;

            $glyph = new FontAwesomeGlyph('angle-left', ['fa-4x', 'fa-fw'], null, 'fas');
            $html[] = '<a href="' . $this->determineUrl($slideshowIndex, $this->getSlideshowAutoPlay()) . '">' .
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
    public function renderSlidshowAutoplay(): string
    {
        $slideshowAutoplay = $this->getSlideshowAutoPlay();

        $html = [];

        if ($slideshowAutoplay)
        {
            if (!$this->isLast())
            {
                $slideshowIndex = $this->getSlideshowIndex() + 1;
                $autoplayUrl = $this->determineUrl($slideshowIndex, 1);
            }
            else
            {
                $autoplayUrl = $this->determineUrl(0, 1);
            }

            $html[] = '<meta http-equiv="Refresh" content="10; url=' . $autoplayUrl . '" />';
        }

        return implode(PHP_EOL, $html);
    }
}