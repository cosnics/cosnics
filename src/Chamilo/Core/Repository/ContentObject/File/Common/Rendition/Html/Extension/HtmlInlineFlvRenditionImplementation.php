<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension;

/**
 *
 * @package
 *          Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension$HtmlInlineFlvRenditionImplementation
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class HtmlInlineFlvRenditionImplementation extends HtmlInlineMediaElementRenditionImplementation
{

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension\HtmlInlineMediaElementRenditionImplementation::getMediaElement()
     */
    public function getSources($parameters)
    {
        return '<source type="video/flv" src="' . $this->getMediaUrl() . '" />';
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
