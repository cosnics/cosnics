<?php
namespace Chamilo\Core\Repository\ContentObject\PhysicalLocation\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\PhysicalLocation\Common\Rendition\HtmlRenditionImplementation;

class HtmlShortRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        $object = $this->get_content_object();
        return '<span>' . htmlentities($object->get_title()) . ' - ' . htmlentities($object->get_location()) . '</span>';
    }
}
