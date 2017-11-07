<?php
namespace Chamilo\Core\Repository\Implementation\Scribd;

use Chamilo\Libraries\Translation\Translation;

class ExternalObjectDisplay extends \Chamilo\Core\Repository\External\ExternalObjectDisplay
{

    public function get_display_properties()
    {
        $object = $this->get_object();
        $properties = parent::get_display_properties();
        if (count($object->get_download_formats()) > 0)
        {
            $properties[Translation::get('DownloadFormats')] = $object->get_download_formats_string();
        }
        $properties[Translation::get('Tags')] = $object->get_tags_string();
        $properties[Translation::get('License')] = $object->get_license_icon();
        
        return $properties;
    }

    public function get_preview($is_thumbnail = false)
    {
        $scribd_object = $this->get_object();
        
        $class = ($is_thumbnail ? 'thumbnail' : 'with_border');
        
        $image_path = $scribd_object->get_url();
        
        $html = array();
        $html[] = '<img class="' . $class . '" src="' . $image_path . '" />';
        return implode(PHP_EOL, $html);
    }
}
