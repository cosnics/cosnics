<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\Assignment\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition::launch($this);
    }

    public function get_description()
    {
        $html = array();
        
        $html[] = '<b>' . Translation::get('StartTime') . ':</b> ';
        $html[] = DatetimeUtilities::format_locale_date(
            Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES), 
            $this->get_content_object()->get_start_time());
        $html[] = '<br />';
        
        $html[] = '<b>' . Translation::get('EndTime') . ':</b> ';
        $html[] = DatetimeUtilities::format_locale_date(
            Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES), 
            $this->get_content_object()->get_end_time());
        $html[] = '<br />';
        
        return implode(PHP_EOL, $html);
    }
}
