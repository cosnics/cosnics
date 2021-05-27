<?php
namespace Chamilo\Core\Repository\ContentObject\Link\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\Link\Common\Rendition\HtmlRenditionImplementation;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition::launch($this);
    }

    public function get_description()
    {
        $object = $this->get_content_object();
        
        $html = [];
        
        $html[] = '<div class="link_url" style="margin-top: 1em;">';
        
        if ($object->get_show_in_iframe())
        {
            $html[] = '<div style="border: 1px solid grey;">';
            $html[] = '<iframe border="0" style="border: 0;" width="100%" height="500"  src="' . $object->get_url() .
                 '"></iframe>';
            $html[] = '</div>';
        }
        else
        {
            $html[] = '<a target="about:blank" href="' . $object->get_url() . '">';
            $html[] = $object->get_url();
            $html[] = '</a>';
        }
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }
}
