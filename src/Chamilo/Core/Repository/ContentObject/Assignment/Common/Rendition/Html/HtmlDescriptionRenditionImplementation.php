<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\Assignment\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\Format\Table\PropertiesTable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition::launch($this);
    }

//    public function get_description()
//    {
//
//        $html = array();
//
//        $properties = array();
//        $properties[Translation::get('StartTime')] =
//            DatetimeUtilities::format_locale_date(
//                Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES),
//                $this->get_content_object()->get_start_time()
//            ) . '</div>';
//        $properties[Translation::get('EndTime')] =
//            DatetimeUtilities::format_locale_date(
//                Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES),
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
