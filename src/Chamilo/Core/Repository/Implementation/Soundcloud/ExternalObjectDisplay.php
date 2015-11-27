<?php
namespace Chamilo\Core\Repository\Implementation\Soundcloud;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ExternalObjectDisplay extends \Chamilo\Core\Repository\External\ExternalObjectDisplay
{

    public function get_display_properties()
    {
        $object = $this->get_object();
        
        $properties = parent :: get_display_properties();
        
        if ($object->get_description())
        {
            $properties[Translation :: get('Description', null, Utilities :: COMMON_LIBRARIES)] = nl2br(
                $object->get_description());
        }
        
        if ($object->get_genre())
        {
            $properties[Translation :: get('Genre')] = $object->get_genre();
        }
        
        if ($object->get_label())
        {
            $properties[Translation :: get('Label')] = $object->get_label();
        }
        
        $properties[Translation :: get('License')] = $object->get_license_icon();
        
        if ($object->get_track_type())
        {
            $properties[Translation :: get('TrackType')] = $object->get_track_type_string();
        }
        
        if ($object->get_bpm())
        {
            $properties[Translation :: get('BeatsPerMinute')] = $object->get_bpm() . ' ' . Translation :: get('Bpm');
        }
        
        return $properties;
    }

    public function get_preview($is_thumbnail = false)
    {
        $object = $this->get_object();
        
        if ($is_thumbnail && $object->get_artwork())
        {
            $class = ($is_thumbnail ? 'thumbnail' : 'with_border');
            
            $html = array();
            $html[] = '<img class="' . $class . '" src="' . $object->get_artwork() . '" />';
            return implode(PHP_EOL, $html);
        }
        elseif (! $is_thumbnail)
        {
            $preview_url = urlencode('http://api.soundcloud.com/tracks/' . $object->get_id());
            
            return '<object height="81" width="100%">
            <param name="movie" value="http://player.soundcloud.com/player.swf?url=' . $preview_url . '&secret_url=false"></param>
            <param name="allowscriptaccess" value="always"></param>
            <embed allowscriptaccess="always" height="81" src="http://player.soundcloud.com/player.swf?url=' .
                 $preview_url . '&secret_url=false" type="application/x-shockwave-flash" width="100%"></embed>
            </object>';
        }
        else
        {
            return parent :: get_preview($is_thumbnail);
        }
    }
}
