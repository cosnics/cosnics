<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension;

use Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineOfficeRenditionImplementation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class HtmlInlineXlsxRenditionImplementation extends HtmlInlineOfficeRenditionImplementation
{

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineOfficeRenditionImplementation::getSizeLimit()
     */
    public function getSizeLimit()
    {
        return 5 * 1024 * 1024;
    }

    /**
     *
     * @return string[]
     */
    public function getViewerFrameClasses()
    {
        return array('office-viewer-frame');
    }

    /**
     *
     * @return boolean
     */
    public function allowsFullScreen()
    {
        return true;
    }
}
