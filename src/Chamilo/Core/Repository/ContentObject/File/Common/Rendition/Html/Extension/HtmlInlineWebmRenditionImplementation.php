<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension;

use Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineMediaElementRenditionImplementation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class HtmlInlineWebmRenditionImplementation extends HtmlInlineMediaElementRenditionImplementation
{

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension\HtmlInlineMediaElementRenditionImplementation::getMediaElement()
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
     * @see \Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension\HtmlInlineMediaElementRenditionImplementation::getMediaElementType()
     */
    public function getMediaElementType()
    {
        return 'video';
    }
}
