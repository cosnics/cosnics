<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Implementation\Rendition;

use Chamilo\Core\Repository\ContentObject\RssFeed\Implementation\RenditionImplementation;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;

class HtmlRenditionImplementation extends RenditionImplementation
{
    /**
     * Renders RSS Feeds
     *
     * @return string
     */
    protected function renderRssFeeds()
    {
        $object = $this->get_content_object();
        $html = array();

        $html[] = ResourceManager::get_instance()->get_resource_html(
            Path::getInstance()->namespaceToFullPath('Chamilo\Core\Repository\ContentObject\RssFeed', true) .
            'Resources/Javascript/RssFeedRenderer/rssFeedRenderer.js'
        );

        $html[] = '<div ng-app="rssFeedRendererApp">';

        $html[] = '<div class="content_object" style="background-image: url(' . $object->get_icon_path() . ');">';
        $html[] = '<div class="title">' . Translation :: get('Description') . '</div>';
        $html[] = '<div class="link_url" style="margin-top: 1em;"><a href="' . htmlentities($object->get_url()) . '">' .
            htmlentities($object->get_url()) . '</a></div>';
        $html[] = '</div>';

        $html[] = '<rss-feed-renderer rss-feed-url="' . $object->get_url() . '" number-of-entries="' .
            $object->get_number_of_entries() . '">';

        $html[] = '<div class="content_object" ng-repeat="entry in main.feedEntries" style="background-image: url(' .
            Theme :: getInstance()->getCommonImagePath('ContentObject/RssFeedItem') . ');">';
        $html[] = '<div class="title">{{ entry.title }}</div>';
        $html[] = '{{ entry.content }}';
        $html[] = '<div class="link_url" style="margin-top: 1em;"><a href="{{ entry.link }}">{{ entry.link }}</a></div>';
        $html[] = '</div>';

        $html[] = '</rss-feed-renderer>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
