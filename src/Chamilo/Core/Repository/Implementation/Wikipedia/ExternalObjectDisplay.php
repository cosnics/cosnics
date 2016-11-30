<?php
namespace Chamilo\Core\Repository\Implementation\Wikipedia;

class ExternalObjectDisplay extends \Chamilo\Core\Repository\External\ExternalObjectDisplay
{

    public function get_preview($is_thumbnail = false)
    {
        $object = $this->get_object();
        $url = $object->get_render_url();
        
        $html = array();
        
        if ($is_thumbnail || ! $url)
        {
            return parent::get_preview($is_thumbnail);
        }
        else
        {
            $html = array();
            $html[] = '<iframe class="preview" src="' . $url . '"></iframe>';
            return implode(PHP_EOL, $html);
        }
    }
}
