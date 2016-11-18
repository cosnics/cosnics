<?php
namespace Chamilo\Core\Repository\ContentObject\Youtube\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\Youtube\Common\Rendition\HtmlRenditionImplementation;

class HtmlPreviewRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return $this->get_video_element();
    }
}
