<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension;

use Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineRenditionImplementation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class HtmlInlineSwfRenditionImplementation extends HtmlInlineRenditionImplementation
{

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineRenditionImplementation::render()
     */
    public function render($parameters)
    {
        $parameters = $this->validateParameters($parameters);
        
        $object = $this->get_content_object();
        $url = \Chamilo\Core\Repository\Manager::get_document_downloader_url(
            $object->get_id(), 
            $object->calculate_security_code()) . '&display=1';
        
        $html = array();
        $html[] = '<object  classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
                            codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0"
                            width="' . $parameters[self::PARAM_WIDTH] . '"
                            height="' . $parameters[self::PARAM_HEIGHT] . '">';
        
        $html[] = '<param name="movie" value="' . $url . '" />';
        $html[] = '<param name="quality" value="high" />';
        $html[] = '<param name="allowFullScreen" value="true" />';
        
        $html[] = '<embed   src="' . $url . '"
                            quality="high"
                            bgcolor="#ffffff"
                            width="' . $parameters[self::PARAM_WIDTH] . '"
                            height="' . $parameters[self::PARAM_HEIGHT] . '"
                            type="application/x-shockwave-flash"
                            allowFullScreen="true"
                            pluginspage="http://www.macromedia.com/go/getflashplayer">';
        $html[] = '</embed>';
        $html[] = '</object>';
        
        return implode(PHP_EOL, $html);
    }
}
