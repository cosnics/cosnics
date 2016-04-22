<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension;

use Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineMediaElementRenditionImplementation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class HtmlInlineMp3RenditionImplementation extends HtmlInlineMediaElementRenditionImplementation
{

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension\HtmlInlineMediaElementRenditionImplementation::getMediaElement()
     */
    public function getSources($parameters)
    {
        return '<source type="audio/mp3" src="' . $this->getMediaUrl() . '" />';
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension\HtmlInlineMediaElementRenditionImplementation::getMediaElementType()
     */
    public function getMediaElementType()
    {
        return 'audio';
    }
}
