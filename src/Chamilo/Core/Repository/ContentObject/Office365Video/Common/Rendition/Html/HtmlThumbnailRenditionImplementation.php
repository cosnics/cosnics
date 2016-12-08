<?php
namespace Chamilo\Core\Repository\ContentObject\Office365Video\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\Office365Video\Common\Rendition\HtmlRenditionImplementation;

class HtmlThumbnailRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return $this->get_video_element(90, 75);
    }
}
