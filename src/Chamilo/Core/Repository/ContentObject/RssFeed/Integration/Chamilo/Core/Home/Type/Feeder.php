<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Integration\Chamilo\Core\Home\Type;

use Chamilo\Core\Home\Architecture\ConfigurableInterface;
use Chamilo\Core\Home\Interfaces\StaticBlockTitleInterface;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Block;

class Feeder extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Block implements ConfigurableInterface,
    StaticBlockTitleInterface
{

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Home\Service\HomeService $homeService
     * @param \Chamilo\Core\Home\Storage\DataClass\Block $block
     * @param string $defaultTitle
     */
    public function __construct(Application $application, HomeService $homeService, Block $block, $defaultTitle = '')
    {
        parent :: __construct($application, $homeService, $block, Translation :: get('Feeder'));
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

        if ($content_object)
        {
            return $content_object->get_title();
        }

        return parent :: getTitle();
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

        $target = $this->getLinkTarget();
        $target = $target ? 'target="' . $target . '"' : 'target="_blank"';
        $icon = Theme :: getInstance()->getImagePath(
            'Chamilo\Core\Repository\ContentObject\RssFeed',
            'Logo/' . Theme :: ICON_MINI);

        $html[] = '<rss-feed-renderer rss-feed-url="' . $content_object->get_url() . '" number-of-entries="' .
             $content_object->get_number_of_entries() . '">';
        $html[] = '<ul class="rss_feeds">';

        $html[] = '<li ng-repeat="entry in main.feedEntries" class="rss_feed_item"' . 'style="background-image: url(' .
             $icon . ')">';
        $html[] = '<a href="{{ entry.link }}" ' . $target . '>{{ entry.title }}</a>';
        $html[] = '</li>';

        $html[] = '</ul>';

        $html[] = '<span style="font-weight: bold;" ng-show="main.feedEntries.length == 0">' .
             Translation :: get('NoFeedsFound') . '</span>';
        $html[] = '</rss-feed-renderer>';

        return implode(PHP_EOL, $html);
    }
}
