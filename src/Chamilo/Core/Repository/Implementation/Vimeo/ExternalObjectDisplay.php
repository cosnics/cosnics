<?php
namespace Chamilo\Core\Repository\Implementation\Vimeo;

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
        $properties[Translation :: get('Tags')] = $this->get_object()->get_tags();

        return $properties;
    }

    public function get_preview($is_thumbnail = false)
    {
        $object = $this->get_object();
        $html = array();
        if ($is_thumbnail)
        {
            $html[] = '<img class="' . 'thumbnail' . '" src="' . $object->get_thumbnail() . '" />';
        }
        else
        {
            $html[] = '<iframe src="http://player.vimeo.com/video/' . $object->get_id() .
                 '" width="400" height="300" frameborder="0"></iframe>';
        }
        return implode(PHP_EOL, $html);
    }
}
