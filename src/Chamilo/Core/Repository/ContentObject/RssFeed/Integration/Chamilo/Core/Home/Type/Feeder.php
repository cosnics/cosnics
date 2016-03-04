<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Integration\Chamilo\Core\Home\Type;

use Chamilo\Core\Home\Architecture\ConfigurableInterface;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;

class Feeder extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Block implements ConfigurableInterface
{

    public function __construct($renderer, $block)
    {
        parent :: __construct($renderer, $block, Translation :: get('Feeder'));
    }

    public function isVisible()
    {
        return true; // i.e.display on homepage when anonymous
    }

    /**
     * Displays the title of the feed or the generic title if no object selected
     *
     * @return string
     */
    public function getTitle()
    {
        $content_object = $this->getObject();

        if($content_object)
        {
            return $content_object->get_title();
        }

        return parent::getTitle();
    }

    /**
     * Returns the html to display when the block is configured.
     *
     * @return string
     */
    public function displayContent()
    {
        $content_object = $this->getObject();

        $html = array();

        $html[] = '<script type="text/javascript">';
        $html[] = '(function(){';
        $html[] = '     var rssFeedRendererApp = angular.module(\'rssFeedRendererApp\', []);';
        $html[] = '     rssFeedRendererApp.value(\'rssFeedUrl\', \'' . $content_object->get_url() . '\');';
        $html[] = '     rssFeedRendererApp.value(\'numberOfEntries\', \'' . $content_object->get_number_of_entries() . '\');';
        $html[] = '})();';
        $html[] = '</script>';

        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->namespaceToFullPath('Chamilo\Core\Repository\ContentObject\RssFeed', true) .
                 'Resources/Javascript/RssFeedRenderer/rssFeedRenderer.js');

        $target = $this->getLinkTarget();
        $target = $target ? 'target="' . $target . '"' : 'target="_blank"';
        $icon = Theme :: getInstance()->getImagePath(
            'Chamilo\Core\Repository\ContentObject\RssFeed',
            'Logo/' . Theme :: ICON_MINI);

        $html[] = '<div ng-app="rssFeedRendererApp" ng-controller="MainController as main">';
        $html[] = '<ul class="rss_feeds">';

        $html[] = '<li ng-repeat="entry in main.feedEntries" class="rss_feed_item"' . 'style="background-image: url(' .
             $icon . ')">';
        $html[] = '<a href="{{ entry.link }}" ' . $target . '>{{ entry.title }}</a>';
        $html[] = '</li>';

        $html[] = '</ul>';

        $html[] = '<span style="font-weight: bold;" ng-show="main.feedEntries.length == 0">' .
             Translation :: get('NoFeedsFound') . '</span>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
