<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Implementation\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\RssFeed\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition :: launch($this);
    }

    public function get_description()
    {
        $object = $this->get_content_object();
        $html = array();

        $html[] = '<div class="content_object" style="background-image: url(' . $object->get_icon_path() . ');">';
        $html[] = '<div class="title">' . Translation :: get('Description') . '</div>';
        $html[] = '<div class="link_url" style="margin-top: 1em;"><a href="' . htmlentities($object->get_url()) . '">' .
             htmlentities($object->get_url()) . '</a></div>';
        $html[] = '</div>';

        $feed = $this->parse_file($object->get_url());

        foreach ($feed as $item)
        {
            $html[] = '<div class="content_object" style="background-image: url(' .
                 Theme :: getInstance()->getCommonImagePath('ContentObject/rss_feed_item') . ');">';
            $html[] = '<div class="title">' . $item['title'] . '</div>';
            $html[] = html_entity_decode($item['description']);
            $html[] = '<div class="link_url" style="margin-top: 1em;"><a href="' . htmlentities($item['link']) . '">' .
                 htmlentities($item['link']) . '</a></div>';
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }
}
