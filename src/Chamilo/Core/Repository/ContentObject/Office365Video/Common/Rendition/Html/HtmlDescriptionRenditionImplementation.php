<?php
namespace Chamilo\Core\Repository\ContentObject\Office365Video\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\Office365Video\Common\Rendition\HtmlRenditionImplementation;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition::launch($this);
    }

    public function get_description()
    {
        return $this->get_video_element();
    }
}
