<?php
namespace Chamilo\Core\Repository\Implementation\Office365;

use Chamilo\Libraries\Platform\Translation;

class ExternalObjectDisplay extends \Chamilo\Core\Repository\External\ExternalObjectDisplay
{

    public function get_display_properties()
    {
        $object = $this->get_object();

        $properties = parent::get_display_properties();
        $properties[Translation::get('LastModifiedBy')] = $object->get_modifier_id();

        return $properties;
    }

    public function get_preview($is_thumbnail = false)
    {
    }
}
