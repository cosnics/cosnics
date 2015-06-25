<?php
namespace Chamilo\Core\Repository\ContentObject\Youtube\Implementation\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\Youtube\Implementation\Rendition\HtmlRenditionImplementation;

class HtmlPreviewRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return $this->get_video_element();
    }
}
