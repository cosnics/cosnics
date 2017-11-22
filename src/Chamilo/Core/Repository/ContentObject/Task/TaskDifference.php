<?php
namespace Chamilo\Core\Repository\ContentObject\Task;

use Chamilo\Core\Repository\Common\ContentObjectDifference;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This class can be used to get the difference between tasks
 */
class TaskDifference extends ContentObjectDifference
{

    public function render()
    {
        $date_format = Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES);
        
        $object = $this->get_object();
        $version = $this->get_version();
        
        $object_string = htmlentities(
            Translation::get('From') . ' ' .
                 DatetimeUtilities::format_locale_date($date_format, $object->get_start_date()) . ' ' .
                 Translation::get('Until') . ' ' .
                 DatetimeUtilities::format_locale_date($date_format, $object->get_due_date()));
        $object_string = explode("\n", strip_tags($object_string));
        
        $version_string = htmlentities(
            Translation::get('From') . ' ' .
                 DatetimeUtilities::format_locale_date($date_format, $version->get_start_date()) . ' ' .
                 DatetimeUtilities::format_locale_date($date_format, $version->get_due_date()));
        $version_string = explode("\n", strip_tags($version_string));
        
        $html = array();
        $html[] = parent::render();
        
        $difference = new \Diff($version_string, $object_string);
        $renderer = new \Diff_Renderer_Html_SideBySide();
        
        $html[] = $difference->Render($renderer);
        
        return implode(PHP_EOL, $html);
    }
}
