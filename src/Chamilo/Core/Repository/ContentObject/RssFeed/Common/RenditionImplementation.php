<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Common;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Libraries\File\Rss\Reader\RssReader;

class RenditionImplementation extends ContentObjectRenditionImplementation
{

    public static function parse_file($url)
    {
        return RssReader::factory()->parse_url($url);
    }
}
