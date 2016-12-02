<?php
namespace Chamilo\Core\Repository\ContentObject\Office365Video\Common\Rendition;

use Chamilo\Core\Repository\ContentObject\Office365Video\Common\RenditionImplementation;

class HtmlRenditionImplementation extends RenditionImplementation
{

    public function get_video_element($width = 600, $height = 480)
    {
        return $this->get_content_object()->getVideoEmbedCode($width, $height);
    }
}
