<?php
namespace Chamilo\Core\Repository\ContentObject\Hotpotatoes\Implementation\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\Hotpotatoes\Implementation\Rendition\HtmlRenditionImplementation;

class HtmlShortRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        $object = $this->get_content_object();
        return '<span class="content_object"><a href="' . htmlentities($object->get_url()) . '">' .
             htmlentities($object->get_title()) . '</a></span>';
    }
}
