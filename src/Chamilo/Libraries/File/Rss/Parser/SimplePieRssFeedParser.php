<?php
namespace Chamilo\Libraries\File\Rss\Parser;

use HTMLPurifier;
use InvalidArgumentException;
use SimplePie\SimplePie;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Psr16Cache;

/**
 * Parses Rss Feeds with SimplePie
 *
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @package Chamilo\Libraries\File\Rss\Parser
 */
class SimplePieRssFeedParser
{

    private HTMLPurifier $purifier;

    private SimplePie $simplePie;

    public function __construct(
        SimplePie $simplePie, HTMLPurifier $purifier, AdapterInterface $cacheAdapter
    )
    {
        $simplePie->set_cache(new Psr16Cache($cacheAdapter));

        $this->simplePie = $simplePie;
        $this->purifier = $purifier;
    }

    /**
     * @param string $url
     * @param int $numberOfEntries
     *
     * @return string[][]
     */
    public function parse($url, $numberOfEntries = 5)
    {
        if (!$url || empty($url) || !$numberOfEntries || $numberOfEntries < 1)
        {
            throw new InvalidArgumentException(
                sprintf('URL %s or number of entries %s invalid', $url, $numberOfEntries)
            );
        }

        $this->simplePie->set_feed_url($url);
        $this->simplePie->set_item_limit($numberOfEntries);
        $this->simplePie->init();

        $feed_items = [];

        for ($i = 0; $i < $this->simplePie->get_item_quantity($numberOfEntries); $i ++)
        {
            $item = [];
            $feed_item = $this->simplePie->get_item($i);
            $item['id'] = $feed_item->get_id();
            $item['title'] = $this->purifier->purify($feed_item->get_title());
            $item['content'] = $this->purifier->purify($feed_item->get_content());
            $item['date'] = $feed_item->get_date();
            $item['link'] = $feed_item->get_link();
            $feed_items[] = $item;
        }

        return $feed_items;
    }
}