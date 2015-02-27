<?php
namespace Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html\Extension;

use Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html\HtmlInlineRenditionImplementation;
use Chamilo\Libraries\File\Path;

class HtmlInlineSwfRenditionImplementation extends HtmlInlineRenditionImplementation
{

    public function render($parameters)
    {
        $object = $this->get_content_object();
        $url = Path :: getInstance()->getBasePath(true) .
             \Chamilo\Core\Repository\Manager :: get_document_downloader_url($object->get_id()) . '&display=1';
        
        $html = array();
        $html[] = '<object  classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
                            codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0"
                            width="' . $parameters[self :: PARAM_WIDTH] . '"
                            height="' . $parameters[self :: PARAM_HEIGHT] .
             '">';
        
        $html[] = '<param name="movie" value="' . $url . '" />';
        $html[] = '<param name="quality" value="high" />';
        
        $html[] = '<embed   src="' . $url . '"
                            quality="high"
                            bgcolor="#ffffff"
                            width="' . $parameters[self :: PARAM_WIDTH] . '"
                            height="' . $parameters[self :: PARAM_HEIGHT] . '"
                            type="application/x-shockwave-flash"
                            pluginspage="http://www.macromedia.com/go/getflashplayer">';
        $html[] = '</embed>';
        $html[] = '</object>';
        
        return implode(PHP_EOL, $html);
    }
}
