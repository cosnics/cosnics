<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class HtmlInlinePptxRenditionImplementation extends HtmlInlineDocxRenditionImplementation
{
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
