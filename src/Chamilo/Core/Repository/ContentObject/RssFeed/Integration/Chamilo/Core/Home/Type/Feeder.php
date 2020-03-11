<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Integration\Chamilo\Core\Home\Type;

use Chamilo\Core\Home\Architecture\ConfigurableInterface;
use Chamilo\Core\Home\Architecture\ContentObjectPublicationBlockInterface;
use Chamilo\Core\Home\Interfaces\StaticBlockTitleInterface;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

class Feeder extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Block
    implements ConfigurableInterface, StaticBlockTitleInterface, ContentObjectPublicationBlockInterface
{

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Home\Service\HomeService $homeService
     * @param \Chamilo\Core\Home\Storage\DataClass\Block $block
     * @param int $source
     * @param string $defaultTitle
     */
    public function __construct(
        Application $application, HomeService $homeService, Block $block, $source = self::SOURCE_DEFAULT,
        $defaultTitle = ''
    )
    {
        parent::__construct($application, $homeService, $block, $source, Translation::get('Feeder'));
    }

    /**
     * Returns the html to display when the block is configured.
     *
     * @return string
     */
    public function displayContent()
    {
        if ($this->getSource() == self::SOURCE_AJAX)
        {
            return Translation::getInstance()->getTranslation(
                'PleaseRefreshPageToSeeChanges', null,
                'Chamilo\Core\Repository\ContentObject\RssFeed\Integration\Chamilo\Core\Home'
            );
        }

        $content_object = $this->getObject();

        $html = array();

        $target = $this->getLinkTarget();
        $target = $target ? 'target="' . $target . '"' : 'target="_blank"';

        $html[] = '<rss-feed-renderer rss-feed-url="' . $content_object->get_url() . '" number-of-entries="' .
            $content_object->get_number_of_entries() . '">';
        $html[] = '<ul class="rss_feeds">';

        $html[] = '<li ng-repeat="entry in main.feedEntries" class="rss_feed_item">';

        $glyph = new NamespaceIdentGlyph(
            'Chamilo\Core\Repository\ContentObject\RssFeed', true, false, false, Theme::ICON_MINI, array()
        );

        $html[] = $glyph->render() . ' ' . '<a href="{{ entry.link }}" ' . $target . '>{{ entry.title }}</a>';
        $html[] = '</li>';

        $html[] = '</ul>';

        $html[] = '<span style="font-weight: bold;" ng-show="main.feedEntries.length == 0">' .
            Translation::get('NoFeedsFound') . '</span>';
        $html[] = '</rss-feed-renderer>';

        return implode(PHP_EOL, $html);
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

        return parent::getTitle();
    }

    public function isVisible()
    {
        return true; // i.e.display on homepage when anonymous
    }
}
