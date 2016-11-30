<?php
namespace Chamilo\Core\Repository\Common\Rendition\Json\Type;

use Chamilo\Core\Repository\Common\Rendition\Json\JsonContentObjectRendition;

class JsonFullContentObjectRendition extends JsonContentObjectRendition
{

    public function render()
    {
        return json_encode($this->get_content_object()->get_default_properties());
    }
}
