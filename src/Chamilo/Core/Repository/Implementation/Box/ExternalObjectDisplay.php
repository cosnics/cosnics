<?php
namespace Chamilo\Core\Repository\Implementation\Box;

class ExternalObjectDisplay extends \Chamilo\Core\Repository\External\ExternalObjectDisplay
{

    public function get_display_properties()
    {
        $object = $this->get_object();
        
        $properties = parent :: get_display_properties();
        return $properties;
    }

    public function get_preview($is_thumbnail = false)
    {
    }
}
