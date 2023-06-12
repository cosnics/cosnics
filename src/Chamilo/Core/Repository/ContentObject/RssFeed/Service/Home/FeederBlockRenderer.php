<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Service\Home;

use Chamilo\Core\Home\Architecture\Interfaces\AnonymousBlockInterface;
use Chamilo\Core\Home\Architecture\Interfaces\ConfigurableBlockInterface;
use Chamilo\Core\Home\Architecture\Interfaces\ContentObjectPublicationBlockInterface;
use Chamilo\Core\Home\Architecture\Interfaces\StaticBlockTitleInterface;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Repository\ContentObject\RssFeed\Storage\DataClass\RssFeed;
use Chamilo\Core\Repository\Service\Home\BlockRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;

class FeederBlockRenderer extends BlockRenderer
    implements ConfigurableBlockInterface, StaticBlockTitleInterface, ContentObjectPublicationBlockInterface,
    AnonymousBlockInterface
{
    public const CONTEXT = RssFeed::CONTEXT;

    public function displayRepositoryContent(Element $block): string
    {
        //        if ($this->getSource() == self::SOURCE_AJAX)
        //        {
        //            return $this->getTranslator()->trans(
        //                'PleaseRefreshPageToSeeChanges', [], RssFeed::CONTEXT
        //            );
        //        }

        /**
         * @var \Chamilo\Core\Repository\ContentObject\RssFeed\Storage\DataClass\RssFeed $rssFeed
         */
        $rssFeed = $this->getObject($block);

        $html = [];

        $target = 'target="_blank"';

        $html[] = '<rss-feed-renderer rss-feed-url="' . $rssFeed->get_url() . '" number-of-entries="' .
            $rssFeed->get_number_of_entries() . '">';
        $html[] = '<ul class="rss_feeds">';

        $html[] = '<li ng-repeat="entry in main.feedEntries" class="rss_feed_item">';

        $glyph = new NamespaceIdentGlyph(
            'Chamilo\Core\Repository\ContentObject\RssFeed', true, false, false, IdentGlyph::SIZE_MINI
        );

        $html[] = $glyph->render() . ' ' . '<a href="{{ entry.link }}" ' . $target . '>{{ entry.title }}</a>';
        $html[] = '</li>';

        $html[] = '</ul>';

        $html[] = '<span style="font-weight: bold;" ng-show="main.feedEntries.length == 0">' .
            $this->getTranslator()->trans('NoFeedsFound', [], RssFeed::CONTEXT) . '</span>';
        $html[] = '</rss-feed-renderer>';

        return implode(PHP_EOL, $html);
    }

    protected function getDefaultTitle(): string
    {
        return $this->getTranslator()->trans('Feeder', [], RssFeed::CONTEXT);
    }

    /**
     * Displays the title of the feed or the generic title if no object selected
     */
    public function getTitle(Element $block, ?User $user = null): string
    {
        $contentObject = $this->getObject($block);

        if ($contentObject)
        {
            return $contentObject->get_title();
        }

        return parent::getTitle($block, $user);
    }
}
