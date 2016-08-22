<?php
namespace Chamilo\Core\Repository\Implementation\Youtube;

use Chamilo\Core\Repository\External\General\Streaming\StreamingMediaExternalObjectDisplay;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

class ExternalObjectDisplay extends StreamingMediaExternalObjectDisplay
{

    public function get_title()
    {
        $object = $this->get_object();
        return '<h3>' . $object->get_title() . ' (' . DatetimeUtilities :: format_seconds_to_minutes($object->get_duration()) .
             ')</h3>';
    }

    public function get_display_properties()
    {
        $properties = parent :: get_display_properties();
        $properties[Translation :: get('Status')] = $this->get_object()->get_status_text();
        $properties[Translation :: get('Category')] = Translation :: get($this->get_object()->get_category());
        $properties[Translation :: get('Tags')] = $this->get_object()->get_tags_string();
        return $properties;
    }

    public function get_preview($is_thumbnail = false)
    {
        $object = $this->get_object();
        $html = array();
        $html[] = '<iframe frameborder="0" height="480" width="600" src="' . $object->get_video_url() .
             '"></iframe>';
        return implode(PHP_EOL, $html);
    }
}
