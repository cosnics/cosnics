<?php
namespace Chamilo\Core\Repository\Common\Rendition\Html\Type;

use Chamilo\Core\Repository\Common\Rendition\Html\HtmlContentObjectRendition;

class HtmlShortContentObjectRendition extends HtmlContentObjectRendition
{

    public function render()
    {
        $object = $this->get_content_object();
        return '<span>' . htmlentities($object->get_title()) . '</span>';
    }
}
