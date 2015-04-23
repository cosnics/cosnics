<?php
namespace Chamilo\Core\Repository\ContentObject\Task\Implementation\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\Task\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition :: launch($this);
    }

    public function get_description()
    {
        $object = $this->get_content_object();
        $date_format = Translation :: get('DateTimeFormatLong', null, Utilities :: COMMON_LIBRARIES);
        
        $prepend = array();
        $repeats = $object->has_frequency();
        
        if ($repeats)
        {
            $prepend[] = '<div class="task_range" style="font-weight: bold;">';
            $prepend[] = Translation :: get('Repeats');
            $prepend[] = ' ';
            $prepend[] = strtolower($object->get_frequency_as_string());
            $prepend[] = ' ';
            $prepend[] = Translation :: get('Until');
            $prepend[] = ' ';
            $prepend[] = DatetimeUtilities :: format_locale_date($date_format, $object->get_until());
            $prepend[] = '</div>';
        }
        else
        {
            $prepend[] = '<div class="task_range" style="font-weight: bold;">';
            $prepend[] = Translation :: get('From');
            $prepend[] = ' ';
            $prepend[] = DatetimeUtilities :: format_locale_date($date_format, $object->get_start_date());
            $prepend[] = ' ';
            $prepend[] = Translation :: get('Until');
            $prepend[] = ' ';
            $prepend[] = DatetimeUtilities :: format_locale_date($date_format, $object->get_due_date());
            $prepend[] = '</div>';
        }
        
        $prepend[] = '<div class="task_range" style="font-style: italic;">';
        $prepend[] = Translation :: get('Priority') . ' : ' . $object->get_priority_as_string() . '<br/>';
        $prepend[] = Translation :: get('TaskType') . ' : ' . $object->get_category_as_string();
        $prepend[] = '</div>';
        
        return implode('', $prepend);
    }
}
