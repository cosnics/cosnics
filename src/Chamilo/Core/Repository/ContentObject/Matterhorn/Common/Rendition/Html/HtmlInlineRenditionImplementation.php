<?php
namespace Chamilo\Core\Repository\ContentObject\Matterhorn\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Matterhorn\Common\Rendition\HtmlRenditionImplementation;

class HtmlInlineRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        $html = array();
        $html[] = '<p>';
        $html[] = ContentObjectRenditionImplementation :: launch(
            $object = $this->get_content_object(), 
            ContentObjectRendition :: FORMAT_HTML, 
            ContentObjectRendition :: VIEW_PREVIEW, 
            $this->get_context());
        $html[] = '</p>';
        
        return implode(PHP_EOL, $html);
    }
}
