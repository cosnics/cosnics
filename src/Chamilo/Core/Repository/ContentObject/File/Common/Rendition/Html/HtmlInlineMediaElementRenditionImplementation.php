<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
abstract class HtmlInlineMediaElementRenditionImplementation extends HtmlInlineMediaRenditionImplementation
{

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineRenditionImplementation::render()
     */
    public function render($parameters)
    {
        $html = array();

        $html[] = ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getPluginPath(Utilities::COMMON_LIBRARIES, true) .
            'MediaElementJS/build/mediaelement-and-player.min.js'
        );

        $html[] = ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getPluginPath(Utilities::COMMON_LIBRARIES, true) .
            'MediaElementJS/build/mediaelementplayer.min.css'
        );

        $html[] = ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath(File::package(), true) . 'MediaElementJS.js'
        );

        $html[] = $this->getMediaElement($parameters);

        $html[] = '<div class="clearfix"></div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param string[] $parameters
     *
     * @return string
     */
    public function getMediaElement($parameters)
    {
        $html = array();

        $html[] = '<div class="media-element-js-container">';

        $html[] = '<' . $this->getMediaElementType() . ' class="media-element-js-element" style="max-width: 100%">';
        $html[] = $this->getSources($parameters);
        $html[] = '</' . $this->getMediaElementType() . '>';

        $html[] = $this->getErrorMessage(false);

        $html[] = '<div class="media-element-js-download">';
        $html[] = $this->renderActions();
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function getMediaUrl()
    {
        $object = $this->get_content_object();

        return Manager::get_document_downloader_url(
                $object->get_id(), $object->calculate_security_code()
            ) . '&display=1';
    }

    /**
     *
     * @param string[] $parameters
     *
     * @return string
     */
    abstract public function getSources($parameters);
}
