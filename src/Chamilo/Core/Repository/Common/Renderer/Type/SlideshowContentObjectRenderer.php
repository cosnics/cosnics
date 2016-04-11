<?php
namespace Chamilo\Core\Repository\Common\Renderer\Type;

use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class SlideshowContentObjectRenderer extends ContentObjectRenderer
{
    const SLIDESHOW_INDEX = 'slideshow';
    const SLIDESHOW_AUTOPLAY = 'autoplay';

    /**
     *
     * @var integer
     */
    private $contentObjectCount;

    /**
     *
     * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    private $contentObject;

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Service\ContentObjectService
     */
    private $contentObjectService;

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
     * @var integer
     */
    private $slideshowIndex;

    /**
     *
     * @var integer
     */
    private $slideshowAutoPlay;

    public function as_html()
    {
        $slideshowIndex = $this->getSlideshowIndex();
        $contentObject = $this->getContentObject();
        $contentObjectCount = $this->getContentObjectCount();

        if ($contentObjectCount == 0)
        {
            $html[] = Display :: normal_message(Translation :: get('NoContentObjectsAvailable'), true);
            return implode(PHP_EOL, $html);
        }

        $html = array();

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12">';

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
        $html[] = ContentObjectRenditionImplementation :: factory(
            $contentObject,
            ContentObjectRendition :: FORMAT_HTML,
            ContentObjectRendition :: VIEW_PREVIEW,
            $this->get_repository_browser())->render();
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
            $parameters = $this->get_parameters();
            $parameters[self :: SLIDESHOW_INDEX] = 0;

            $html[] = '<a href="' . $this->get_url($parameters) .
                 '"><span class="glyphicon glyphicon-step-backward"></span></a>';

            $parameters = $this->get_parameters();
            $parameters[self :: SLIDESHOW_INDEX] = $this->getSlideshowIndex() - 1;

            $html[] = '<a href="' . $this->get_url($parameters) .
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
            $parameters = $this->get_parameters();
            $parameters[self :: SLIDESHOW_INDEX] = $this->getSlideshowIndex() + 1;

            $html[] = '<a href="' . $this->get_url($parameters) .
                 '"><span class="glyphicon glyphicon-triangle-right"></span></a>';

            $parameters = $this->get_parameters();
            $parameters[self :: SLIDESHOW_INDEX] = $this->getContentObjectCount() - 1;

            $html[] = '<a href="' . $this->get_url($parameters) .
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
        $actionsToolBar->add_items($this->get_content_object_actions($this->getContentObject()));

        if ($slideshowAutoplay)
        {
            $parameters[self :: SLIDESHOW_INDEX] = $slideshowIndex;
            $parameters[self :: SLIDESHOW_AUTOPLAY] = 0;

            $actionsToolBar->add_item(
                new ToolbarItem(
                    Translation :: get('Stop', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Stop'),
                    $this->get_url($parameters),
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $parameters[self :: SLIDESHOW_INDEX] = $slideshowIndex;
            $parameters[self :: SLIDESHOW_AUTOPLAY] = 1;

            $actionsToolBar->add_item(
                new ToolbarItem(
                    Translation :: get('Play', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Play'),
                    $this->get_url($parameters),
                    ToolbarItem :: DISPLAY_ICON));
        }

        $actionsToolBarRenderer = new ButtonToolBarRenderer($actionsToolBar->convertToButtonToolBar(false));

        return $actionsToolBarRenderer->render();
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function getContentObject()
    {
        if (! isset($this->contentObject))
        {
            $workspace = $this->get_repository_browser()->getWorkspace();

            $this->contentObject = $this->getContentObjectService()->getContentObjectsForWorkspace(
                $workspace,
                ConditionFilterRenderer :: factory(FilterData :: get_instance($workspace), $workspace),
                1,
                $this->getSlideshowIndex())->next_result();
        }
        return $this->contentObject;
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
            $parameters = array(self :: SLIDESHOW_AUTOPLAY => 1);

            if (! $this->isLast())
            {
                $parameters[self :: SLIDESHOW_INDEX] = $this->getSlideshowIndex() + 1;
                $autoplayUrl = $this->get_url($parameters);
            }
            else
            {
                $parameters[self :: SLIDESHOW_INDEX] = 0;
                $autoplayUrl = $this->get_url($parameters);
            }

            $html[] = '<meta http-equiv="Refresh" content="10; url=' . $autoplayUrl . '" />';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return integer
     */
    public function getContentObjectCount()
    {
        if (! isset($this->contentObjectCount))
        {
            $workspace = $this->get_repository_browser()->getWorkspace();

            $this->contentObjectCount = $this->getContentObjectService()->countContentObjectsForWorkspace(
                $workspace,
                ConditionFilterRenderer :: factory(FilterData :: get_instance($workspace), $workspace));
        }

        return $this->contentObjectCount;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Service\ContentObjectService
     */
    public function getContentObjectService()
    {
        if (! isset($this->contentObjectService))
        {
            $this->contentObjectService = new ContentObjectService(new ContentObjectRepository());
        }

        return $this->contentObjectService;
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
     * @return integer
     */
    public function getSlideshowIndex()
    {
        if (! isset($this->slideshowIndex))
        {
            $this->slideshowIndex = $this->get_repository_browser()->getRequest()->query->get(
                self :: SLIDESHOW_INDEX,
                0);
        }

        return $this->slideshowIndex;
    }

    public function getSlideshowAutoPlay()
    {
        if (! isset($this->slideshowAutoPlay))
        {
            $this->slideshowAutoPlay = $this->get_repository_browser()->getRequest()->query->get(
                self :: SLIDESHOW_AUTOPLAY,
                0);
        }

        return $this->slideshowAutoPlay;
    }
}
