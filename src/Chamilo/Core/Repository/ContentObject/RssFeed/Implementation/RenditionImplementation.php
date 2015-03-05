<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Implementation;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Libraries\File\Rss\Reader\RssReader;

class RenditionImplementation extends ContentObjectRenditionImplementation
{

    public static function parse_file($url)
    {
        $rss_reader = RssReader :: factory();
        return $rss_reader->parse_url($url);
    }
}
