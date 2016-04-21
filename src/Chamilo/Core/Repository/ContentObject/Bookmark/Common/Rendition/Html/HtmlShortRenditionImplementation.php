<?php
namespace Chamilo\Core\Repository\ContentObject\Bookmark\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\Bookmark\Common\Rendition\HtmlRenditionImplementation;

class HtmlShortRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        $object = $this->get_content_object();
        return '<span class="content_object"><a target="about:blank" href="' . htmlentities($object->get_url()) . '">' .
             htmlentities($object->get_title()) . '</a></span>';
    }
}
