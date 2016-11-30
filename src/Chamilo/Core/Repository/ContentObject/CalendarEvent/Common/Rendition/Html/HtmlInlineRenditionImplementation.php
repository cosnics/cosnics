<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\CalendarEvent\Common\Rendition\HtmlRenditionImplementation;

class HtmlInlineRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return $this->get_string();
    }
}
