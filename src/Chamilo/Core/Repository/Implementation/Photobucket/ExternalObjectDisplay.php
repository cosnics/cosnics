<?php
namespace Chamilo\Core\Repository\Implementation\Photobucket;

use Chamilo\Libraries\Platform\Translation;

class ExternalObjectDisplay extends \Chamilo\Core\Repository\External\ExternalObjectDisplay
{

    public function get_display_properties()
    {
        $properties = parent :: get_display_properties();
        $properties[Translation :: get('Tags')] = $this->get_object()->get_tags_string();
        return $properties;
    }

    public function get_preview($is_thumbnail = false)
    {
        $object = $this->get_object();
        $size = ($is_thumbnail ? $object->get_thumbnail() : $object->get_url());
        $class = ($is_thumbnail ? 'thumbnail' : 'with_border');
        
        $html = array();
        $html[] = '<img class="' . $class . '" src="' . $size . '" />';
        return implode(PHP_EOL, $html);
    }
}
