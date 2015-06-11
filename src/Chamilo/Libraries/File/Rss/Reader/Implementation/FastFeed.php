<?php
namespace Chamilo\Libraries\File\Rss\Reader\Implementation;

use Chamilo\Libraries\File\Rss\Reader\RssReader;
use FastFeed\Factory;
use FastFeed\Processor\LimitProcessor;

/**
 *
 * @package Chamilo\Libraries\File\Rss\Reader\Implementation
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FastFeed extends RssReader
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
        $fastFeed = Factory :: create();
        $fastFeed->addFeed('default', $url);
        $fastFeed->pushProcessor(new LimitProcessor($number_of_items));

        // Process items
        $items = array();

        foreach ($fastFeed->fetch() as $fastFeedItem)
        {
            $items[] = array(
                'title' => $fastFeedItem->getName(),
                'description' => $fastFeedItem->getContent(),
                'link' => $fastFeedItem->getSource());
        }

        return $items;
    }
}