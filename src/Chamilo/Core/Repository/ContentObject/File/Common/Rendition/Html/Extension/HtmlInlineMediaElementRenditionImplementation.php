<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension;

use Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineMediaRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ActionBar\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

abstract class HtmlInlineMediaElementRenditionImplementation extends HtmlInlineMediaRenditionImplementation
{

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineRenditionImplementation::render()
     */
    public function render($parameters)
    {
        $html = array();

        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath(Utilities :: COMMON_LIBRARIES, true) .
                 'Plugin/MediaElementJS/build/mediaelement-and-player.min.js');
        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath(Utilities :: COMMON_LIBRARIES, true) .
                 'Plugin/MediaElementJS/build/mediaelementplayer.css');
        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath(File :: package(), true) . 'MediaElementJS.js');

        $html[] = $this->getMediaElement();

        $html[] = '<div class="clearfix"></div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param string[] $parameters
     * @return string
     */
    public function getMediaElement($parameters)
    {
        $html = array();

        $html[] = '<div class="media-element-js-container">';

        $html[] = '<' . $this->getMediaElementType() . ' class="media-element-js-element">';
        $html[] = $this->getSources($parameters);
        $html[] = '</' . $this->getMediaElementType() . '>';

        $html[] = $this->getErrorMessage(false);

        $html[] = '<div class="media-element-js-download">';
        $html[] = $this->renderDownloadAction();
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param string[] $parameters
     * @return string
     */
    abstract public function getSources($parameters);

    /**
     *
     * @return string
     */
    public function getMediaUrl()
    {
        $object = $this->get_content_object();

        return \Chamilo\Core\Repository\Manager :: get_document_downloader_url(
            $object->get_id(),
            $object->calculate_security_code()) . '&display=1';
    }

    public function renderDownloadAction($classes)
    {
        $object = $this->get_content_object();
        $name = $object->get_filename();

        $url = \Chamilo\Core\Repository\Manager :: get_document_downloader_url(
            $object->get_id(),
            $object->calculate_security_code());

        $label = '<small>(' . Filesystem :: format_file_size($object->get_filesize()) . ')</small>';

        $buttonToolBar = new ButtonToolBar();
        $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        $buttonToolBar->addItem(
            new Button(
                Translation :: get('DownloadFile', array('LABEL' => $label)),
                new BootstrapGlyph('download'),
                $url,
                Button :: DISPLAY_ICON_AND_LABEL,
                false,
                $classes));

        return $buttonToolBarRenderer->render();
    }
}
