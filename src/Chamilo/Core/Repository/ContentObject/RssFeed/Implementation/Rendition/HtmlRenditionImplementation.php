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
     * Helper function to add javascript initialization
     */
    protected function addJavascriptInitialization()
    {
        $object = $this->get_content_object();

        $html[] = '<script type="text/javascript">';
        $html[] = '(function(){';
        $html[] = '     var rssFeedRendererApp = angular.module(\'rssFeedRendererApp\', []);';
        $html[] = '     rssFeedRendererApp.value(\'rssFeedUrl\', \'' . $object->get_url() . '\');';
        $html[] = '     rssFeedRendererApp.value(\'numberOfEntries\', \'' . 10 . '\');';
        $html[] = '})();';
        $html[] = '</script>';

        $html[] = ResourceManager::get_instance()->get_resource_html(
            Path::getInstance()->namespaceToFullPath('Chamilo\Core\Repository\ContentObject\RssFeed', true) .
            'Resources/Javascript/RssFeedRenderer/rssFeedRenderer.js'
        );

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders RSS Feeds
     *
     * @return string
     */
    protected function renderRssFeeds()
    {
        $object = $this->get_content_object();
        $html = array();

        $html[] = '<div class="content_object" style="background-image: url(' . $object->get_icon_path() . ');">';
        $html[] = '<div class="title">' . Translation :: get('Description') . '</div>';
        $html[] = '<div class="link_url" style="margin-top: 1em;"><a href="' . htmlentities($object->get_url()) . '">' .
            htmlentities($object->get_url()) . '</a></div>';
        $html[] = '</div>';

        $html[] = $this->addJavascriptInitialization();

        $html[] = '<div ng-app="rssFeedRendererApp" ng-controller="MainController as main">';

        $html[] = '<div class="content_object" ng-repeat="entry in main.feedEntries" style="background-image: url(' .
            Theme :: getInstance()->getCommonImagePath('ContentObject/RssFeedItem') . ');">';
        $html[] = '<div class="title">{{ entry.title }}</div>';
        $html[] = '{{ entry.content }}';
        $html[] = '<div class="link_url" style="margin-top: 1em;"><a href="{{ entry.link }}">{{ entry.link }}</a></div>';
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
