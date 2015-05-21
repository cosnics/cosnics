<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Implementation\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\Webpage\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\File\Path;

class HtmlInlineRenditionImplementation extends HtmlRenditionImplementation
{

    public function render($parameters)
    {
        $object = $this->get_content_object();
        $url = \Chamilo\Core\Repository\Manager :: get_document_downloader_url($object->get_id()) . '&display=1';
        
        $html = array();
        
        $html[] = '<div style="border: 1px solid grey;"><iframe border="0" style="border: 0;"
                width="100%" height="500"  src="' . $url .
             '&display=1"></iframe></div></div>';
        
        return implode(PHP_EOL, $html);
    }
}
