<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\RssFeed\Common\Rendition\HtmlRenditionImplementation;

class HtmlInlineRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return $this->renderRssFeeds();
    }
}
