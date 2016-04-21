<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class HtmlInlineWmvRenditionImplementation extends HtmlInlineMediaElementRenditionImplementation
{

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension\HtmlInlineMediaElementRenditionImplementation::getMediaElement()
     */
    public function getSources($parameters)
    {
        return '<source type="video/wmv" src="' . $this->getMediaUrl() . '" />';
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension\HtmlInlineMediaElementRenditionImplementation::getMediaElementType()
     */
    public function getMediaElementType()
    {
        return 'video';
    }
}
