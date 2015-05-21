<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Implementation\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\CalendarEvent\Implementation\Rendition\HtmlRenditionImplementation;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition :: launch($this);
    }

    public function get_description()
    {
        return $this->get_string();
    }
}
