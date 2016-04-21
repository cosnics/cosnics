<?php
namespace Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html\Extension;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html\Extension
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class HtmlInlineMp3RenditionImplementation extends HtmlInlineMediaElementVideoRenditionImplementation
{

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html\Extension\HtmlInlineMediaElementVideoRenditionImplementation::getMediaElement()
     */
    public function getSources($parameters)
    {
        return '<source type="audio/mp3" src="' . $this->getMediaUrl() . '" />';
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html\Extension\HtmlInlineMediaElementVideoRenditionImplementation::getMediaElementType()
     */
    public function getMediaElementType()
    {
        return 'audio';
    }
}
