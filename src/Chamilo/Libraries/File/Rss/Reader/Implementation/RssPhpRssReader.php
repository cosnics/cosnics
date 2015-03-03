<?php
namespace Chamilo\Libraries\File\Rss\Reader\Implementation;

/**
 * Class that reads an rss feed using the last rss plugin
 * 
 * @package common\libraries
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RssPhpRssReader
{

    /**
     * Parses a url and returns the rss items
     * 
     * @param string $url
     * @param int $number_of_items
     *
     * @return string[]
     */
    public function parse_url($url, $number_of_items = 5)
    {
        $rss = new RssPhpWrapper();
        
        $rss->load($url);
        
        $items = $rss->getItems();
        if (count($items) > $number_of_items)
        {
            array_splice($items, $number_of_items);
        }
        
        $feed = array();
        $feed['items'] = $items;
        
        return $feed;
    }
}