<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\RssFeed\Common\Rendition\HtmlRenditionImplementation;

class HtmlFullThumbnailRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        $object = $this->get_content_object();
        return '<span><a href="' . htmlentities($object->get_url()) . '">' . htmlentities($object->get_title()) .
             '</a></span>';
    }
}
