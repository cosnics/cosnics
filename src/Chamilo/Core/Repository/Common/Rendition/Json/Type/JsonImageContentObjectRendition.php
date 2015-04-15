<?php
namespace Chamilo\Core\Repository\Common\Rendition\Json\Type;

use Chamilo\Core\Repository\Common\Rendition\Json\JsonContentObjectRendition;

class JsonImageContentObjectRendition extends JsonContentObjectRendition
{
    const PROPERTY_URL = 'url';

    public function render()
    {
        return array(self :: PROPERTY_URL => '');
    }
}
