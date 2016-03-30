<?php
namespace Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\File\Path;

class HtmlPreviewRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        $object = $this->get_content_object();

        if ($object->is_image())
        {
            $url = Path :: getInstance()->getBasePath(true) .
                 \Chamilo\Core\Repository\Manager :: get_document_downloader_url(
                    $object->get_id(),
                    $object->calculate_security_code());
            return '<img src="' . $url . '" alt="" style="max-width: 800px; border: 1px solid #f0f0f0;"/>';
        }
        else
        {
            return ContentObjectRendition :: launch($this);
        }
    }
}
