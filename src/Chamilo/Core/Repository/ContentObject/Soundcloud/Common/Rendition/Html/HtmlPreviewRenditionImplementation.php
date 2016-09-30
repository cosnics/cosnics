<?php
namespace Chamilo\Core\Repository\ContentObject\Soundcloud\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\Soundcloud\Common\Rendition\HtmlRenditionImplementation;

class HtmlPreviewRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return $this->get_track_element();
    }
}
