<?php
namespace Chamilo\Core\Repository\ContentObject\Soundcloud\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\Soundcloud\Common\Rendition\HtmlRenditionImplementation;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition :: launch($this);
    }

    public function get_description()
    {
        $object = $this->get_content_object();
        
        $track_element = $this->get_track_element();
        
        return '<div class="link_url" style="margin-top: 1em;">' . $track_element . '</div>';
    }
}
