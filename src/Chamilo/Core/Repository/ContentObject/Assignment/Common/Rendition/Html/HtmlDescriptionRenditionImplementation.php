<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\Assignment\Common\Rendition\HtmlRenditionImplementation;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition::launch($this);
    }

//    public function get_description()
//    {
//
//        $html = [];
//
//        $properties = [];
//        $properties[Translation::get('StartTime')] =
//            DatetimeUtilities::getInstance()->formatLocaleDate(
//                Translation::get('DateTimeFormatLong', null, StringUtilities::LIBRARIES),
//                $this->get_content_object()->get_start_time()
//            ) . '</div>';
//        $properties[Translation::get('EndTime')] =
//            DatetimeUtilities::getInstance()->formatLocaleDate(
//                Translation::get('DateTimeFormatLong', null, StringUtilities::LIBRARIES),
//                $this->get_content_object()->get_end_time()
//            );
//
//        $table = new PropertiesTable($properties);
//
//        $html[] = $table->toHtml();
//
//        return implode(PHP_EOL, $html);
//    }
}
