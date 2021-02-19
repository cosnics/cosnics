<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\RssFeed\Common\Rendition\HtmlRenditionImplementation;

class HtmlFullThumbnailRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        $object = $this->get_content_object();
        $url = $this->getSanitizedUrl($object->get_url());

        return '<span><a href="' . $url . '">' . $url . '</a></span>';
    }
}
