<?php
namespace Chamilo\Core\Repository\Implementation\Slideshare;

use Chamilo\Libraries\Utilities\DatetimeUtilities;

class ExternalObjectDisplay extends \Chamilo\Core\Repository\External\ExternalObjectDisplay
{

    public function get_title()
    {
        $object = $this->get_object();
        return '<h3>' . $object->get_title() . ' (' .
             DatetimeUtilities::format_seconds_to_minutes($object->get_duration()) . ')</h3>';
    }

    public function get_display_properties()
    {
        $properties = parent::get_display_properties();
        
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
            $html[] = $object->get_embed();
        }
        return implode(PHP_EOL, $html);
    }
}
