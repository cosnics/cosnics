<?php
namespace Chamilo\Core\Repository\Implementation\Flickr;

use Chamilo\Libraries\Platform\Translation;

class ExternalObjectDisplay extends \Chamilo\Core\Repository\External\ExternalObjectDisplay
{

    public function get_display_properties()
    {
        $object = $this->get_object();
        
        $properties = parent :: get_display_properties();
        $properties[Translation :: get('OwnerName')] = $object->get_owner_name();
        $properties[Translation :: get('AvailableSizes')] = $object->get_available_sizes_string();
        $properties[Translation :: get('Tags')] = $object->get_tags_string();
        $properties[Translation :: get('License')] = $object->get_license_icon();
        
        return $properties;
    }

    public function get_preview($is_thumbnail = false)
    {
        $object = $this->get_object();
        $size = ($is_thumbnail ? ExternalObject :: SIZE_SQUARE : ExternalObject :: SIZE_MEDIUM);
        $class = ($is_thumbnail ? 'thumbnail' : 'with_border');
        
        $html = array();
        $html[] = '<img class="' . $class . '" src="' . $object->get_url($size) . '" />';
        return implode(PHP_EOL, $html);
    }
}
