<?php

namespace Chamilo\Libraries\File\Rss\Parser;

class RssFeedParserFactory
{
    const SIMPLE_PIE_FEED_PARSER = 'simplePie';

    /**
     * @param \HTMLPurifier $purifier
     * @param String $type
     *
     * @return RssFeedParserInterface
     */
    public static function create(\HTMLPurifier $purifier, $type)
    {
        $feed_parser = null;

        if ($type == self::SIMPLE_PIE_FEED_PARSER)
        {
            $feed_parser = new SimplePieRssFeedParser(new \SimplePie(), $purifier);
        }

        return $feed_parser;
    }
} 