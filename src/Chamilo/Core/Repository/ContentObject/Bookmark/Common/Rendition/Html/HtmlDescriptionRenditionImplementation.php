<?php
namespace Chamilo\Core\Repository\ContentObject\Bookmark\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\Bookmark\Common\Rendition\HtmlRenditionImplementation;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition::launch($this);
    }

    public function get_description()
    {
        $object = $this->get_content_object();
        return '<div class="bookmark_url" style="margin-top: 1em;"><a target="about:blank" href="' .
             htmlentities($object->get_url()) . '">' . htmlentities($object->get_url()) . '</a></div>';
    }
}
