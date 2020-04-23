<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\Webpage\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Core\Repository\Manager;

class HtmlFullThumbnailRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        $object = $this->get_content_object();
        $url = Manager::get_document_downloader_url(
            $object->get_id(), $object->calculate_security_code()
        );

        return '<span><a href="' . htmlentities($url) . '">' . htmlentities($object->get_title()) . '</a></span>';
    }
}
