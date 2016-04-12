<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Implementation\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\CalendarEvent\Implementation\Rendition\HtmlRenditionImplementation;

class HtmlInlineRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return $this->get_string();
    }
}
