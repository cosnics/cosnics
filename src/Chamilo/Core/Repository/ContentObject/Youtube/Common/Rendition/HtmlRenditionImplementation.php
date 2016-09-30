<?php
namespace Chamilo\Core\Repository\ContentObject\Youtube\Common\Rendition;

use Chamilo\Core\Repository\ContentObject\Youtube\Common\RenditionImplementation;

class HtmlRenditionImplementation extends RenditionImplementation
{

    public function get_video_element($width = 600, $height = 480)
    {
        $object = $this->get_content_object();
        // $video_url = $object->get_video_url();
        
        return '<iframe frameborder="0" style="margin-bottom: 1em;" height="' . $height . '" width="' . $width .
             '" src="' . $object->get_video_url() . '"></iframe>';
    }
}
