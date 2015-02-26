<?php
namespace Chamilo\Core\Repository\Implementation\GoogleDocs;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
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
        $properties[Translation :: get('SharedWith')] = $object->get_acl();
        
        return $properties;
    }

    public function get_preview($is_thumbnail = false)
    {
        if ($is_thumbnail)
        {
            return parent :: get_preview($is_thumbnail);
        }
        else
        {
            $object = $this->get_object();
            
            switch ($object->get_type())
            {
                case 'pdf' :
                    $format = 'pdf';
                    break;
                case 'document' :
                case 'presentation' :
                    $format = 'png';
                    break;
                case 'spreadsheet' :
                    $format = 'html';
                    break;
                default :
                    $format = null;
                    break;
            }
            
            $preview_system_path = Path :: getInstance()->getTemporaryPath('google_docs') . $object->get_id() . '.' .
                 $format;
            
            if (! file_exists($preview_system_path))
            {
                $preview = $object->get_content_data($format);
                Filesystem :: write_to_file($preview_system_path, $preview);
            }
            
            $url = Path :: getInstance()->getTemporaryPath('google_docs', true) . $object->get_id() . '.' . $format;
            
            if ($url)
            {
                $html = array();
                $html[] = '<iframe class="preview" src="' . $url . '"></iframe>';
                return implode("\n", $html);
            }
            else
            {
                return parent :: get_preview($is_thumbnail);
            }
        }
    }
}
