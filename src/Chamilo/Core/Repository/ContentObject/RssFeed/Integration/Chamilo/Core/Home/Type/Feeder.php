<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Integration\Chamilo\Core\Home\Type;

use Chamilo\Core\Repository\ContentObject\RssFeed\Implementation\RenditionImplementation;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Home\Architecture\ConfigurableInterface;

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
     * Returns the html to display when the block is configured.
     *
     * @return string
     */
    public function displayContent()
    {
        $content_object = $this->getObject();

        $html = array();
        $feed = RenditionImplementation :: parse_file($content_object->get_url());

        if ($feed)
        {
            $target = $this->getLinkTarget();
            $target = $target ? 'target="' . $target . '"' : 'target="_blank"';
            $icon = Theme :: getInstance()->getImagePath(
                'Chamilo\Core\Repository\ContentObject\RssFeed',
                'Logo/' . Theme :: ICON_MINI);
            $html[] = '<div class="tool_menu">';
            $html[] = '<ul class="rss_feeds">';

            $count_valid = 0;

            foreach ($feed as $item)
            {
                if (! $item['link'] || ! $item['title'])
                {
                    continue;
                }

                $count_valid ++;

                $html[] = '<li class="rss_feed_item" style="background-image: url(' . $icon . ')"><a href="' . htmlentities(
                    $item['link']) . '" ' . $target . '>' . $item['title'] . '</a></li>';
            }

            $html[] = '</ul>';
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
            $html[] = '<br />';
        }

        if (! $feed || $count_valid == 0)
        {
            $html[] = '<span style="font-weight: bold;">' . Translation :: get('NoFeedsFound') . '</span>';
        }

        return '<div style="height: 4px;"></div>' . implode(PHP_EOL, $html);
    }
}
