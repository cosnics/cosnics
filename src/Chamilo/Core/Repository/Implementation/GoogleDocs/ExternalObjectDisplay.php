<?php
namespace Chamilo\Core\Repository\Implementation\GoogleDocs;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

class ExternalObjectDisplay extends \Chamilo\Core\Repository\External\ExternalObjectDisplay
{

    public function get_display_properties()
    {
        $object = $this->get_object();

        $properties = parent :: get_display_properties();
        $properties[Translation :: get('LastViewed')] = DatetimeUtilities :: format_locale_date(
            null,
            $object->get_viewed());
        $properties[Translation :: get('LastModifiedBy')] = $object->get_modifier_id();

        return $properties;
    }

    public function get_preview($is_thumbnail = false)
    {
        if ($this->get_object()->get_content())
        {
            $html = array();
            $html[] = '<iframe class="preview" src="' . $this->get_object()->get_content() . '"></iframe>';
            return implode(PHP_EOL, $html);
        }
        else
        {
            return parent :: get_preview($is_thumbnail);
        }
    }
}
