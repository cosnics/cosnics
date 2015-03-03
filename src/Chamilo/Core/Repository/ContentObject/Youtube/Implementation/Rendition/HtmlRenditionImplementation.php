<?php
namespace Chamilo\Core\Repository\ContentObject\Youtube\Implementation\Rendition;

use Chamilo\Core\Repository\ContentObject\Youtube\Implementation\RenditionImplementation;

class HtmlRenditionImplementation extends RenditionImplementation
{

    public function get_video_element($width = 425, $height = 344)
    {
        $object = $this->get_content_object();
        // $video_url = $object->get_video_url();
        
        return '<embed style="margin-bottom: 1em;" height="' . $height . '" width="' . $width .
             '" type="application/x-shockwave-flash" src="' . $object->get_video_url() . '"></embed>';
    }
}
