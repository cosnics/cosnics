<?php
namespace Chamilo\Core\Repository\ContentObject\Vimeo\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\Vimeo\Common\Rendition\HtmlRenditionImplementation;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition :: launch($this);
    }

    public function get_description()
    {
        $object = $this->get_content_object();
        
        $url = $object->get_video_url();
        
        $video_element = '<embed style="margin-bottom: 1em;" type="application/x-shockwave-flash" height="300" width="400" src="' .
             $url . '"></embed>';
        
        return '<div class="link_url" style="margin-top: 1em;">' . $video_element . '</div>';
    }
}
