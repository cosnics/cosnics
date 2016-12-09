<?php
namespace Chamilo\Core\Repository\ContentObject\Youtube\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\Youtube\Common\Rendition\HtmlRenditionImplementation;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition::launch($this);
    }

    public function get_description()
    {
        $object = $this->get_content_object();
        $video_element = $this->get_video_element();
        
        return '<div class="link_url" style="margin-top: 1em;">' . $video_element . '<br/><a href="' .
             htmlentities($object->get_video_url()) . '">' . htmlentities($object->get_video_url()) . '</a></div>';
    }
}
