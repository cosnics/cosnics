<?php
namespace Chamilo\Core\Repository\ContentObject\Bookmark\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\Bookmark\Common\Rendition\HtmlRenditionImplementation;

class HtmlInlineRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        $object = $this->get_content_object();
        return '<div class="bookmark_url" style="margin-top: 1em;"><a target="about:blank" href="' .
             htmlentities($object->get_url()) . '">' . htmlentities($object->get_url()) . '</a></div>';
    }
}
