<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension;

use Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineRenditionImplementation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class HtmlInlineHtmlRenditionImplementation extends HtmlInlineRenditionImplementation
{

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineRenditionImplementation::render()
     */
    public function render($parameters)
    {
        $object = $this->get_content_object();
        $url = \Chamilo\Core\Repository\Manager::get_document_downloader_url(
            $object->get_id(), 
            $object->calculate_security_code()) . '&display=1';
        
        $html = array();
        
        $html[] = '<div class="text-container">';
        $html[] = '<iframe class="text-frame" src="' . $url .
             '" sandbox="allow-forms allow-pointer-lock allow-same-origin allow-scripts">';
        $html[] = '</iframe>';
        $html[] = $this->renderActions();
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }
}
