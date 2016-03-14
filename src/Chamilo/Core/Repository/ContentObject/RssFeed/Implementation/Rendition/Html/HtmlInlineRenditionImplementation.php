<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Implementation\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\RssFeed\Implementation\Rendition\HtmlRenditionImplementation;

class HtmlInlineRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return $this->renderRssFeeds();
    }
}
