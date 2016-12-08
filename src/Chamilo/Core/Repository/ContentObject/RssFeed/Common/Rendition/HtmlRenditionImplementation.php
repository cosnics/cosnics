<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Common\Rendition;

use Chamilo\Core\Repository\ContentObject\RssFeed\Common\RenditionImplementation;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

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
        
        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->namespaceToFullPath('Chamilo\Core\Repository\ContentObject\RssFeed', true) .
                 'Resources/Javascript/RssFeedRenderer/rssFeedRenderer.js');
        
        $html[] = '<ul class="list-group" ng-app="rssFeedRendererApp">';
        
        $html[] = '<rss-feed-renderer rss-feed-url="' . $object->get_url() . '" number-of-entries="' .
             $object->get_number_of_entries() . '">';
        
        $html[] = '<li class="list-group-item" ng-repeat="entry in main.feedEntries">';
        
        $html[] = '<div class="list-group-item-heading"><h3 class="panel-title">{{ entry.title }}</h3></div>';
        
        $html[] = '<span ng-bind-html="entry.content"></span>';
        $html[] = '<div class="link_url" style="margin-top: 1em;"><a href="{{ entry.link }}">{{ entry.link }}</a></div>';
        $html[] = '</li>';
        
        $html[] = '</rss-feed-renderer>';
        
        $html[] = '</ul>';
        
        return implode(PHP_EOL, $html);
    }
}
