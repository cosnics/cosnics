<?php

namespace Chamilo\Libraries\File\Rss\Parser;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;

/**
 * Parses Rss Feeds with SimplePie
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SimplePieRssFeedParser implements RssFeedParserInterface
{

    /**
     *
     * @var \SimplePie
     */
    private $simplePie;

    /**
     *
     * @var \HTMLPurifier
     */
    private $purifier;

    /**
     * Constructor
     *
     * @param \SimplePie $simplePie
     * @param \HTMLPurifier $purifier
     */
    public function __construct(\SimplePie $simplePie, \HTMLPurifier $purifier)
    {
        $cachePath = Path::getInstance()->getCachePath() . 'rss';
        if (!is_dir($cachePath))
        {
            Filesystem::create_dir($cachePath);
        }

        $simplePie->set_cache_location($cachePath);

        $this->simplePie = $simplePie;
        $this->purifier = $purifier;
    }

    /**
     * Parses a given url with a given amount of entries
     */
    public function parse($url, $number_entries = 5)
    {
        if (!$url || empty($url) || !$number_entries || $number_entries < 1)
        {
            throw new \InvalidArgumentException(
                sprintf('URL %s or number of entries %s invalid', $url, $number_entries)
            );
        }

        $this->simplePie->set_feed_url($url);
        $this->simplePie->set_item_limit($number_entries);
        $this->simplePie->init();

        $feed_items = array();

        for ($i = 0; $i < $this->simplePie->get_item_quantity($number_entries); $i ++)
        {
            $item = array();
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