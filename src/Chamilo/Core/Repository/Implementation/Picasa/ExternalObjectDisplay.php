<?php
namespace Chamilo\Core\Repository\Implementation\Picasa;

use Chamilo\Libraries\Platform\Translation;

class ExternalObjectDisplay extends \Chamilo\Core\Repository\External\ExternalObjectDisplay
{

    public function get_display_properties()
    {
        $object = $this->get_object();
        
        $properties = parent :: get_display_properties();
        $properties[Translation :: get('AvailableSizes')] = $object->get_available_sizes_string();
        $properties[Translation :: get('Tags')] = $object->get_tags_string();
        $properties[Translation :: get('License')] = $object->get_license_icon();
        
        // Overwrite the original owner value, since Picasa gives us a bit more info
        $properties[Translation :: get('Owner')] = $object->get_owner_string();
        
        return $properties;
    }

    public function get_preview($is_thumbnail = false)
    {
        $object = $this->get_object();
        $size = ($is_thumbnail ? ExternalObject :: SIZE_THUMBNAIL : ExternalObject :: SIZE_MEDIUM);
        $class = ($is_thumbnail ? 'thumbnail' : 'with_border');
        
        $html = array();
        $html[] = '<img class="' . $class . '" src="' . $object->get_url($size) . '" />';
        return implode(PHP_EOL, $html);
    }
}
