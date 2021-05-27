<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\Webpage\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Core\Repository\Manager;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition::launch($this);
    }

    public function get_description()
    {
        $object = $this->get_content_object();
        $url = Manager::get_document_downloader_url(
            $object->get_id(), 
            $object->calculate_security_code()) . '&display=1';
        
        $html = [];
        
        $html[] = '<div style="border: 1px solid grey;">';
        $html[] = '<iframe border="0" style="border: 0;"
                width="100%" height="500"  src="' . $url . '"></iframe>';
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }
}
