<?php
namespace Chamilo\Core\Repository\ContentObject\Slideshare\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\Slideshare\Common\Rendition\HtmlRenditionImplementation;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition :: launch($this);
    }

    public function get_description()
    {
        $object = $this->get_content_object();
        return $object->get_embed();
    }
}
