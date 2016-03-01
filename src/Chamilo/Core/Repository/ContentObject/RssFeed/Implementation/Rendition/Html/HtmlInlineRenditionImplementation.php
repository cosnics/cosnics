<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Implementation\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\RssFeed\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\File\Rss\Reader\RssReader;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class HtmlInlineRenditionImplementation extends HtmlRenditionImplementation
{
    public function render()
    {
        return $this->renderRssFeeds();
    }
}
