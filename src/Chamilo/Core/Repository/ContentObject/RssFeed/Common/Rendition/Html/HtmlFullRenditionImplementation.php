<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\RssFeed\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;

class HtmlFullRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        $object = $this->get_content_object();
        $url = $this->getSanitizedUrl($object->get_url());

        $html = array();
        
        $html[] = '<div class="panel panel-default panel-rss-feed">';
        
        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">' . $object->get_icon_image() . ' ' . $object->get_title() . '</h3>';
        $html[] = '</div>';
        
        $html[] = '<div class="panel-body">';
        $renderer = new ContentObjectResourceRenderer($this, $object->get_description());
        $html[] = $renderer->run();
        $html[] = '<a href="' . $url . '">' . $url . '</a>';
        $html[] = '</div>';
        
        $html[] = ContentObjectRenditionImplementation::launch(
            $object, 
            ContentObjectRendition::FORMAT_HTML, 
            ContentObjectRendition::VIEW_INLINE,
            $this->get_context());
        
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }
}
