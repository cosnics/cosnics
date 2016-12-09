<?php
namespace Chamilo\Libraries\File\Rss\Parser;

interface RssFeedParserInterface
{

    public function parse($url, $number_entries);
} 