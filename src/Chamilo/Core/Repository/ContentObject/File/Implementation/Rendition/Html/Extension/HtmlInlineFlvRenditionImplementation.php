<?php
namespace Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html\Extension;

class HtmlInlineFlvRenditionImplementation extends HtmlInlineMediaElementVideoRenditionImplementation
{

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html\Extension\HtmlInlineMediaElementVideoRenditionImplementation::getMediaElement()
     */
    public function getSources($parameters)
    {
        return '<source type="video/flv" src="' . $this->getMediaUrl() . '" />';
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
