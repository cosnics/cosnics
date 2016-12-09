<?php
namespace Chamilo\Core\Repository\ContentObject\Youtube\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\Youtube\Common\Rendition\HtmlRenditionImplementation;

class HtmlShortRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        $object = $this->get_content_object();
        return '<span><a target="about:blank" href="' . htmlentities($object->get_video_url()) . '">' .
             htmlentities($object->get_title()) . '</a></span>';
    }
}
