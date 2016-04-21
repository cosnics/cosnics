<?php
namespace Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html\Extension;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html\Extension
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class HtmlInlineWebmRenditionImplementation extends HtmlInlineMediaElementVideoRenditionImplementation
{

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html\Extension\HtmlInlineMediaElementVideoRenditionImplementation::getMediaElement()
     */
    public function getSources($parameters)
    {
        $html = array();

        $html[] = '<source type="video/webm" src="' . $this->getMediaUrl() . '" />';
        $html[] = '<source type="video/ogg" src="' . $this->getMediaUrl() . '" />';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html\Extension\HtmlInlineMediaElementVideoRenditionImplementation::getMediaElementType()
     */
    public function getMediaElementType()
    {
        return 'video';
    }
}
