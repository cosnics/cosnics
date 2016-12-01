<?php
namespace Chamilo\Core\Repository\Common\Rendition\Xml\Type;

use Chamilo\Core\Repository\Common\Rendition\Xml\XmlContentObjectRendition;

class XmlPreviewContentObjectRendition extends XmlContentObjectRendition
{

    public function __construct($context, $content_object)
    {
    }

    public function render()
    {
        return 'test';
    }
}
