<?php
namespace Chamilo\Core\Repository\Common\Rendition\Html\Type;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Common\Rendition\Html\HtmlContentObjectRendition;

class HtmlFullContentObjectRendition extends HtmlContentObjectRendition
{

    public function render()
    {
        $object = $this->get_content_object();

        $html = array();

        $html[] = '<div class="panel panel-default">';

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">' . $object->get_icon_image() . ' ' . $object->get_title() . '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';
        $html[] = ContentObjectRenditionImplementation :: launch(
            $object,
            ContentObjectRendition :: FORMAT_HTML,
            ContentObjectRendition :: VIEW_DESCRIPTION,
            $this->get_context());
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}