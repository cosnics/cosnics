<?php
namespace Chamilo\Libraries\File\Rss\Parser;

interface RssFeedParserInterface
{

    /**
     * Parses a given url with a given amount of entries
     *
     * @param string $url
     * @param integer $numberOfEntries
     *
     * @return string[][]
     * @throw \InvalidArgumentException
     */
    public function parse($url, $numberOfEntries);
}