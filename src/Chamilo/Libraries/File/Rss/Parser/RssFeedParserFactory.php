<?php
namespace Chamilo\Libraries\File\Rss\Parser;

use HTMLPurifier;
use SimplePie;

/**
 *
 * @package Chamilo\Libraries\File\Rss\Parser
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RssFeedParserFactory
{
    const SIMPLE_PIE_FEED_PARSER = 'simplePie';

    /**
     *
     * @param \HTMLPurifier $purifier
     * @param string $type
     *
     * @return RssFeedParserInterface
     */
    public static function create(HTMLPurifier $purifier, $type)
    {
        $feedParser = null;

        if ($type == self::SIMPLE_PIE_FEED_PARSER)
        {
            $feedParser = new SimplePieRssFeedParser(new SimplePie(), $purifier);
        }

        return $feedParser;
    }
}