<?php
namespace Chamilo\Libraries\Format\Slideshow;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package Chamilo\Libraries\Format\Slideshow
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class SlideshowRenderer
{
    const PARAM_INDEX = 'slideshow';
    const PARAM_AUTOPLAY = 'autoplay';

    /**
     *
     * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    private $contentObject;

    /**
     *
     * @var integer
     */
    private $contentObjectCount;

    /**
     *
     * @var \Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation
     */
    private $contentObjectRenditionImplementation;

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    private $slideActions;

    /**
     *
     * @var string[]
     */
    private $actionParameters;

    /**
     *
     * @var integer
     */
    private $slideshowIndex;

    /**
     *
     * @var integer
     */
    private $slideshowAutoPlay;

    /**
     *
     * @var boolean
     */
    private $isLast;

    /**
     *
     * @var boolean
     */
    private $isFirst;

    /**
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param integer $contentObjectCount
     * @param \Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation $contentObjectRenditionImplementation
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem[] $slideActions
     * @param string[] $actionParameters
     * @param integer $slideshowIndex
     * @param integer $slideshowAutoplay
     */
    public function __construct(ContentObject $contentObject = null, $contentObjectCount, $contentObjectRenditionImplementation,
        $slideActions, $actionParameters, $slideshowIndex, $slideshowAutoplay)
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
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function getContentObject()
    {
        return $this->contentObject;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     */
    public function setContentObject(ContentObject $contentObject)
    {
        $this->contentObject = $contentObject;
    }

    /**
     *
     * @return integer
     */
    public function getContentObjectCount()
    {
        return $this->contentObjectCount;
    }

    /**
     *
     * @param integer $contentObjectCount
     */
    public function setContentObjectCount($contentObjectCount)
    {
        $this->contentObjectCount = $contentObjectCount;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation
     */
    public function getContentObjectRenditionImplementation()
    {
        return $this->contentObjectRenditionImplementation;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation $contentObjectRenditionImplementation
     */
    public function setContentObjectRenditionImplementation(
        ContentObjectRenditionImplementation $contentObjectRenditionImplementation)
    {
        $this->contentObjectRenditionImplementation = $contentObjectRenditionImplementation;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    public function getSlideActions()
    {
        return $this->slideActions;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem[] $slideActions
     */
    public function setSlideActions($slideActions)
    {
        $this->slideActions = $slideActions;
    }

    /**
     *
     * @return string[]
     */
    public function getActionParameters()
    {
        return $this->actionParameters;
    }

    /**
     *
     * @param string[] $actionParameters
     */
    public function setActionParameters($actionParameters)
    {
        $this->actionParameters = $actionParameters;
    }

    /**
     *
     * @return integer
     */
    public function getSlideshowIndex()
    {
        return $this->slideshowIndex;
    }

    /**
     *
     * @param integer $slideshowIndex
     */
    public function setSlideshowIndex($slideshowIndex)
    {
        $this->slideshowIndex = $slideshowIndex;
    }

    /**
     *
     * @return integer
     */
    public function getSlideshowAutoPlay()
    {
        return $this->slideshowAutoPlay;
    }

    /**
     *
     * @param integer $slideshowAutoPlay
     */
    public function setSlideshowAutoPlay($slideshowAutoPlay)
    {
        $this->slideshowAutoPlay = $slideshowAutoPlay;
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        $slideshowIndex = $this->getSlideshowIndex();
        $contentObject = $this->getContentObject();
        $contentObjectCount = $this->getContentObjectCount();

        if ($contentObjectCount == 0)
        {
            $html[] = Display::normal_message(Translation::get('SlideshowNoContentAvailable'), true);
            return implode(PHP_EOL, $html);
        }

        $html = array();

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12">';

        $html[] = '<div class="alert alert-info">' . Translation::get('BrowserWarningPreview') . '</div>';

        $html[] = '<div class="panel panel-default panel-slideshow">';

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">' . htmlspecialchars($contentObject->get_title()) . ' - ' .
             ($slideshowIndex + 1) . '/' . $contentObjectCount . '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';

        $html[] = '<table class="table-slideshow">';
        $html[] = '<tbody>';
        $html[] = '<tr>';

        $html[] = '<td class="control control-left">';
        $html[] = $this->renderPreviousNavigation();
        $html[] = '</td>';

        $html[] = '<td class="thumbnail-container">';
        $html[] = $this->getContentObjectRenditionImplementation()->render();
        $html[] = '</td>';

        $html[] = '<td class="control control-right">';
        $html[] = $this->renderNextNavigation();
        $html[] = '</td>';

        $html[] = '</tr>';
        $html[] = '</tbody>';
        $html[] = '</table>';

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
     *
     * @return string
     */
    public function renderPreviousNavigation()
    {
        $html = array();

        if (! $this->isFirst())
        {
            $html[] = '<a href="' . $this->determineUrl(0, $this->getSlideshowAutoPlay()) .
                 '"><span class="glyphicon glyphicon-step-backward"></span></a>';

            $slideshowIndex = $this->getSlideshowIndex() - 1;

            $html[] = '<a href="' . $this->determineUrl($slideshowIndex, $this->getSlideshowAutoPlay()) .
                 '"><span class="glyphicon glyphicon-triangle-left"></span></a>';
        }
        else
        {
            $html[] = '<span class="glyphicon glyphicon-step-backward disabled"></span>';
            $html[] = '<span class="glyphicon glyphicon-triangle-left disabled"></span>';
        }

        return implode('', $html);
    }

    /**
     *
     * @return string
     */
    public function renderNextNavigation()
    {
        $html = array();

        if (! $this->isLast())
        {
            $slideshowIndex = $this->getSlideshowIndex() + 1;

            $html[] = '<a href="' . $this->determineUrl($slideshowIndex, $this->getSlideshowAutoPlay()) .
                 '"><span class="glyphicon glyphicon-triangle-right"></span></a>';

            $slideshowIndex = $this->getContentObjectCount() - 1;

            $html[] = '<a href="' . $this->determineUrl($slideshowIndex, $this->getSlideshowAutoPlay()) .
                 '"><span class="glyphicon glyphicon-step-forward"></span></a>';
        }
        else
        {
            $html[] = '<span class="glyphicon glyphicon-triangle-right disabled"></span>';
            $html[] = '<span class="glyphicon glyphicon-step-forward disabled"></span>';
        }

        return implode('', $html);
    }

    /**
     *
     * @return string
     */
    public function renderButtonToolbar()
    {
        $slideshowIndex = $this->getSlideshowIndex();
        $slideshowAutoplay = $this->getSlideshowAutoPlay();

        $actionsToolBar = new Toolbar();
        $actionsToolBar->add_items($this->getSlideActions());

        if ($slideshowAutoplay)
        {
            $actionsToolBar->add_item(
                new ToolbarItem(
                    Translation::get('Stop', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Stop'),
                    $this->determineUrl($slideshowIndex, 0),
                    ToolbarItem::DISPLAY_ICON));
        }
        else
        {
            $actionsToolBar->add_item(
                new ToolbarItem(
                    Translation::get('Play', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Play'),
                    $this->determineUrl($slideshowIndex, 1),
                    ToolbarItem::DISPLAY_ICON));
        }

        $actionsToolBarRenderer = new ButtonToolBarRenderer($actionsToolBar->convertToButtonToolBar(false));

        return $actionsToolBarRenderer->render();
    }

    /**
     *
     * @return string
     */
    public function renderSlidshowAutoplay()
    {
        $slideshowAutoplay = $this->getSlideshowAutoPlay();

        $html = array();

        if ($slideshowAutoplay)
        {
            if (! $this->isLast())
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

    /**
     *
     * @return boolean
     */
    public function isLast()
    {
        if (! isset($this->isLast))
        {
            $this->isLast = ($this->getSlideshowIndex() == ($this->getContentObjectCount() - 1));
        }

        return $this->isLast;
    }

    /**
     *
     * @return boolean
     */
    public function isFirst()
    {
        if (! isset($this->isFirst))
        {
            $this->isFirst = ($this->getSlideshowIndex() == 0);
        }
        return $this->isFirst;
    }

    /**
     *
     * @param integer $slideshowIndex
     * @param integer $slideshowAutoPlay
     * @return string
     */
    public function determineUrl($slideshowIndex, $slideshowAutoPlay)
    {
        $parameters = $this->getActionParameters();

        $parameters[self::PARAM_INDEX] = $slideshowIndex;
        $parameters[self::PARAM_AUTOPLAY] = $slideshowAutoPlay;

        $redirect = new Redirect($parameters);
        return $redirect->getUrl();
    }
}