<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Implementation\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\RssFeed\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;

class HtmlFullRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        $object = $this->get_content_object();

        $html = array();

        $html[] = '<div class="panel panel-default panel-rss-feed">';

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">' . $object->get_icon_image() . ' ' . $object->get_title() . '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';
        $html[] = '<a href="' . htmlentities($object->get_url()) . '">' . htmlentities($object->get_url()) . '</a>';
        $html[] = '</div>';

        $html[] = ContentObjectRenditionImplementation :: launch(
            $object,
            ContentObjectRendition :: FORMAT_HTML,
            ContentObjectRendition :: VIEW_DESCRIPTION,
            $this->get_context());

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
