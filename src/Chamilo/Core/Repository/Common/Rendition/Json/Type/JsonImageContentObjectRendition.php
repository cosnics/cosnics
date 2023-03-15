<?php
namespace Chamilo\Core\Repository\Common\Rendition\Json\Type;

use Chamilo\Core\Repository\Common\Rendition\Json\JsonContentObjectRendition;

class JsonImageContentObjectRendition extends JsonContentObjectRendition
{
    public const PROPERTY_URL = 'url';

    public function render()
    {
        return [self::PROPERTY_URL => ''];
    }
}
