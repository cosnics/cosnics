<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\File\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\Utilities\StringUtilities;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition :: launch($this);
    }

    public function get_description()
    {
        $object = $this->get_content_object();

        $class = __NAMESPACE__ . '\Extension\HtmlInline' .
             (string) StringUtilities :: getInstance()->createString($object->get_extension())->upperCamelize() .
             'RenditionImplementation';

        if (! class_exists($class))
        {
            $document_type = $object->determine_type();
            $class = __NAMESPACE__ . '\Type\HtmlInline' .
                 (string) StringUtilities :: getInstance()->createString($document_type)->upperCamelize() .
                 'RenditionImplementation';
        }

        $rendition = new $class($this->get_context(), $this->get_content_object());
        return $rendition->render();
    }
}
