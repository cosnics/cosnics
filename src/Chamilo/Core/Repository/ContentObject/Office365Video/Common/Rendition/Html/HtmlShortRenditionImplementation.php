<?php
namespace Chamilo\Core\Repository\ContentObject\Office365Video\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\Office365Video\Common\Rendition\HtmlRenditionImplementation;

class HtmlShortRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        $object = $this->get_content_object();
        return '<span>' . htmlentities($object->get_title()) . '</span>';
    }
}
