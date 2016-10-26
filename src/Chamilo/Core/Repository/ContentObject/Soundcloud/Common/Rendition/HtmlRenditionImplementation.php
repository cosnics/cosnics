<?php
namespace Chamilo\Core\Repository\ContentObject\Soundcloud\Common\Rendition;

use Chamilo\Core\Repository\ContentObject\Soundcloud\Common\RenditionImplementation;

class HtmlRenditionImplementation extends RenditionImplementation
{

    public function get_track_element($width = '100%', $height = '81')
    {
        $object = $this->get_content_object();
        
        $preview_url = urlencode($object->get_track_api_uri());
        
        $html = array();
        $html[] = '<object height="' . $height . '" width="' . $width . '">';
        $html[] = '<param name="movie" value="' . $object->get_track_player_uri() . '"></param>';
        $html[] = '<param name="allowscriptaccess" value="always"></param>';
        $html[] = '<embed allowscriptaccess="always" height="' . $height . '" src="' . $object->get_track_player_uri() .
             '" type="application/x-shockwave-flash" width="' . $width . '"></embed>';
        $html[] = '</object>';
        
        return implode(PHP_EOL, $html);
    }
}
