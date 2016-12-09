<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\Webpage\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\Utilities\Utilities;

class HtmlShortRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        $object = $this->get_content_object();
        $url = \Chamilo\Core\Repository\Manager::get_document_downloader_url(
            $object->get_id(), 
            $object->calculate_security_code());
        
        return '<span><a href="' . Utilities::htmlentities($url) . '">' . Utilities::htmlentities($object->get_title()) .
             '</a></span>';
    }
}
